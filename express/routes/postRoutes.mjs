import express from "express";
import Post from "../models/Post.mjs";
import User from "../models/User.mjs";
import Comment from "../models/Comment.mjs";
import Like from "../models/Like.mjs";
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
	const likes = await Like.find({ postId: post._id });
	let isLiked = false;
	if (req?.user) {
		isLiked = await Like.findOne({
			userId: req.user.id,
			postId: post._id,
		});
	}
	res.render("postSingle", { post, user, comments, likes, isLiked });
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

router.post("/like/:id", authMiddleware, async (req, res) => {
	try {
		const like = new Like({ userId: req.user.id, postId: req.params.id });
		await like.save();
		res.redirect(`/post/id/${req.params.id}`);
	} catch (error) {
		// TODO ici prend lerreur du Like.save dans Like.mjs
		res.status(500).json(errors.VOTING);
	}
});

router.get("/card/:id", async (req, res) => {
	try {
		const post = await Post.findById(req.params.id);
		const user = await User.findById(post.userId);
		const lastComments = await Comment.find({ postId: post._id })
			.populate("userId")
			.sort({ createdAt: -1 })
			.limit(1);
		const likes = await Like.find({ postId: post._id });
		let isLiked = false;
		if (req?.user) {
			isLiked = await Like.findOne({
				userId: req.user.id,
				postId: post._id,
			});
		}
		res.render("includes/postCard", {
			post,
			user,
			likes,
			isLiked,
			lastComment: lastComments[0],
		});
	} catch (error) {
		res.status(500).json(errors.GETTING_POSTS);
	}
});

router.get("/random", async (req, res) => {
	try {
		const posts = await Post.aggregate([{ $sample: { size: 1 } }]);
		await Promise.all(
			posts.map(async (post) => {
				post.user = await User.findById(post.userId);
				post.lastComment = await Comment.findOne({
					postId: post._id,
				})
					.populate("userId")
					.sort({ createdAt: -1 });
				post.likes = await Like.find({ postId: post._id });
				if (req?.user) {
					post.isLiked = await Like.findOne({
						userId: req.user.id,
						postId: post._id,
					});
				}
			})
		);
		res.render("includes/postCard", {
			post: posts[0],
		});
	} catch (error) {
		res.status(500).json(errors.GETTING_POSTS);
	}
});

router.get("/webcam", authMiddleware, (req, res) => {
	res.render("postWebcam");
});

export default router;
