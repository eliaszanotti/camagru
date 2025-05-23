import express from "express";
import authRoutes from "./auth/index.mjs";
import postRoutes from "./post/index.mjs";
import galleryRoutes from "./gallery/index.mjs";
import { authMiddleware } from "../middleware/authMiddleware.mjs";
import User from "../models/User.mjs";
import Post from "../models/Post.mjs";
import Comment from "../models/Comment.mjs";
import Like from "../models/Like.mjs";
import { errors } from "../utils/errors.mjs";

const router = express.Router();

router.get("/", async (req, res) => {
	try {
		const page = parseInt(req.params.page, 10) || 0;
		const skipCount = page * 6;
		const posts = await Post.find({ isPublished: true })
			.populate("userId")
			.sort({ createdAt: -1 })
			.skip(skipCount)
			.limit(6);
		console.log(posts.map((post) => post._id));
		await Promise.all(
			posts.map(async (post) => {
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
			})
		);
		res.render("index", {
			posts: posts,
		});
	} catch (error) {
		res.status(500).json(errors.GETTING_POSTS);
	}
});

router.get("/profil", authMiddleware, async (req, res) => {
	const userId = req.user.id;
	const user = await User.findById(userId);
	if (!user) {
		return res.status(404).render("404", { title: "404" });
	}
	res.render("profil", { user });
});

router.use("/auth", authRoutes);
router.use("/post", postRoutes);
router.use("/gallery", galleryRoutes);

export default router;
