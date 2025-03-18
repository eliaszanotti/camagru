import express from "express";
import Post from "../models/Post.mjs";
import User from "../models/User.mjs";
import Comment from "../models/Comment.mjs";
import Vote from "../models/Vote.mjs";
import { errors } from "../utils/errors.mjs";
import { authMiddleware } from "../middleware/authMiddleware.mjs";
import postPublishRoute from "./postPublish.mjs";

const router = express.Router();
router.use(express.json());
router.use(postPublishRoute);

router.get("/id/:id", async (req, res) => {
	const post = await Post.findById(req.params.id);
	const user = await User.findById(post.userId);
	const comments = await Comment.find({ postId: post._id })
		.populate("userId")
		.sort({ createdAt: -1 });

	console.log(comments);
	res.render("postSingle", { post, user, comments });
});

router.post("/comment/:id", async (req, res) => {
	const { content } = req.body;
	const post = await Post.findById(req.params.id);
	const comment = new Comment({
		content,
		postId: post._id,
		userId: req.user.id,
	});
	await comment.save();
	res.redirect(`/post/id/${post._id}`);
});

router.get("/random", async (req, res) => {
	try {
		const posts = await Post.aggregate([{ $sample: { size: 1 } }]);
		if (posts.length === 0) {
			return res.status(404).json(errors.NO_POSTS);
		}
		const user = await User.findById(posts[0].userId);
		const lastComment = await Comment.find({ postId: posts[0]._id })
			.sort({ createdAt: -1 })
			.limit(1);
		const isLiked = await Vote.findOne({
			userId: req.user.id,
			postId: posts[0]._id,
		});
		console.log(lastComment);
		res.render("includes/postCard", {
			post: posts[0],
			user: user,
			lastComment: lastComment,
			isLiked: isLiked,
		});
	} catch (error) {
		res.status(500).json(errors.GETTING_POSTS);
	}
});

router.get("/webcam", authMiddleware, (req, res) => {
	res.render("postWebcam");
});

router.post("/vote", authMiddleware, async (req, res) => {
	const { postId } = req.body;
	const userId = req.user.id;

	try {
		const vote = new Vote({ userId, postId });
		await vote.save();
		res.status(200).json({ success: true });
	} catch (error) {
		// TODO ici prend lerreur du Vote.save dans Vote.mjs
		res.status(500).json(errors.VOTING);
	}
});

export default router;
