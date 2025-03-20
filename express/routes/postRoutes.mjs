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

router.get("/id/:id", authMiddleware, async (req, res) => {
	const post = await Post.findById(req.params.id);
	const user = await User.findById(post.userId);
	const comments = await Comment.find({ postId: post._id })
		.populate("userId")
		.sort({ createdAt: -1 });
	let isLiked = false;
	if (req?.user) {
		isLiked = await Vote.findOne({
			userId: req.user.id,
			postId: post._id,
		});
	}
	res.render("postSingle", { post, user, comments, isLiked });
});

router.post("/comment/:id", authMiddleware, async (req, res) => {
	const { content } = req.body;
	try {
		const comment = new Comment({
			content,
			postId: req.params.id,
			userId: req.user.id,
		});
		await comment.save();
		res.redirect(`/post/id/${req.params.id}`);
	} catch (error) {
		// TODO ici prend lerreur du Comment.save dans Comment.mjs
		res.status(500).json(errors.VOTING);
	}
});

router.post("/vote/:id", authMiddleware, async (req, res) => {
	try {
		const vote = new Vote({ userId: req.user.id, postId: req.params.id });
		await vote.save();
		res.redirect(`/post/id/${req.params.id}`);
	} catch (error) {
		// TODO ici prend lerreur du Vote.save dans Vote.mjs
		res.status(500).json(errors.VOTING);
	}
});

router.get("/random", async (req, res) => {
	try {
		const posts = await Post.aggregate([{ $sample: { size: 1 } }]);
		if (posts.length === 0) {
			return res.status(404).json(errors.NO_POSTS);
		}
		const user = await User.findById(posts[0].userId);
		const lastComments = await Comment.find({ postId: posts[0]._id })
			.populate("userId")
			.sort({ createdAt: -1 })
			.limit(1);
		let isLiked = false;
		if (req?.user) {
			isLiked = await Vote.findOne({
				userId: req.user.id,
				postId: posts[0]._id,
			});
		}
		res.render("includes/postCard", {
			post: posts[0],
			user: user,
			lastComment: lastComments[0],
			isLiked: isLiked,
		});
	} catch (error) {
		res.status(500).json(errors.GETTING_POSTS);
	}
});

router.get("/webcam", authMiddleware, (req, res) => {
	res.render("postWebcam");
});

export default router;
