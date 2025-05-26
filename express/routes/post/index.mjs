import express from "express";
import Post from "../../models/Post.mjs";
import Comment from "../../models/Comment.mjs";
import Like from "../../models/Like.mjs";
import User from "../../models/User.mjs";
import { errors } from "../../utils/errors.mjs";
import { authMiddleware } from "../../middleware/authMiddleware.mjs";
import transporter from "../../utils/emailTransporter.mjs";
import { newCommentMailOptions } from "../../utils/mailOptions.mjs";

const router = express.Router();
router.use(express.json());

router.get("/id/:id", authMiddleware, async (req, res) => {
	try {
		const post = await Post.findById(req.params.id).populate("userId");
		post.comments = await Comment.find({ postId: post._id })
			.populate("userId")
			.sort({ createdAt: -1 });
		post.lastComment = post.comments[0];
		post.likes = await Like.find({ postId: post._id });
		if (req?.user) {
			post.isLiked = await Like.findOne({
				userId: req.user.id,
				postId: post._id,
			});
		}
		res.render("postSingle", { post });
	} catch (error) {
		res.status(500).json(errors.GETTING_POSTS);
	}
});

router.post("/comment/:id", authMiddleware, async (req, res) => {
	const { content } = req.body;

	if (!content || content.trim() === "") {
		return res.redirect(`/post/id/${req.params.id}`);
	}

	try {
		const comment = new Comment({
			content,
			postId: req.params.id,
			userId: req.user.id,
		});
		await comment.save();

		const post = await Post.findById(req.params.id);
		if (post) {
			const postOwner = await User.findById(post.userId);
			if (postOwner && postOwner.email) {
				try {
					const mailOptions = newCommentMailOptions(postOwner, post);
					await transporter.sendMail(mailOptions);
				} catch (emailError) {
					return res.status(500).json(errors.EMAIL_SEND_FAILED);
				}
			}
		}

		res.redirect(`/post/id/${req.params.id}`);
	} catch (error) {
		res.status(500).json(errors.COMMENTING);
	}
});

router.post("/like/:id", authMiddleware, async (req, res) => {
	try {
		const like = new Like({ userId: req.user.id, postId: req.params.id });
		await like.save();
		res.redirect(`/post/id/${req.params.id}`);
	} catch (error) {
		res.status(500).json(errors.LIKING);
	}
});

router.post("/unlike/:id", authMiddleware, async (req, res) => {
	try {
		await Like.deleteOne({ userId: req.user.id, postId: req.params.id });
		res.redirect(`/post/id/${req.params.id}`);
	} catch (error) {
		res.status(500).json(errors.UNLIKING);
	}
});

router.post("/publish/:id", authMiddleware, async (req, res) => {
	try {
		const post = await Post.findById(req.params.id);
		if (!post) {
			return res.status(404).json({ message: "Post not found" });
		}

		if (post.userId.toString() !== req.user.id) {
			return res.status(403).json({ message: "Not authorized" });
		}

		post.isPublished = true;
		await post.save();
		res.redirect("/gallery/edit/" + post._id);
	} catch (error) {
		console.error(error);
		res.status(500).json(errors.PUBLISHING_POST);
	}
});

router.post("/unpublish/:id", authMiddleware, async (req, res) => {
	try {
		const post = await Post.findById(req.params.id);
		if (!post) {
			return res.status(404).json({ message: "Post not found" });
		}

		if (post.userId.toString() !== req.user.id) {
			return res.status(403).json({ message: "Not authorized" });
		}

		post.isPublished = false;
		await post.save();
		res.redirect("/gallery/edit/" + post._id);
	} catch (error) {
		console.error(error);
		res.status(500).json(errors.PUBLISHING_POST);
	}
});

export default router;
