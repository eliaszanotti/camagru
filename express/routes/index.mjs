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
		const postsPerPage = 9;
		const page = parseInt(req.query.page - 1, 10) || 0;
		const skipCount = page * postsPerPage;
		const posts = await Post.find({ isPublished: true })
			.populate("userId")
			.sort({ createdAt: -1 })
			.skip(skipCount)
			.limit(postsPerPage);
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
		const totalPages = Math.ceil(
			(await Post.countDocuments({ isPublished: true })) / postsPerPage
		);
		res.render("index", {
			posts: posts,
			title: `Podium - ${page + 1}`,
			totalPages: totalPages,
			currentPage: page + 1,
		});
	} catch (error) {
		res.status(500).json(errors.GETTING_POSTS);
	}
});

router.get("/profile", authMiddleware, async (req, res) => {
	const userId = req.user.id;
	const user = await User.findById(userId);
	if (!user) {
		return res.status(404).render("404", { title: "404" });
	}
	res.render("profile", { user });
});

router.get(
	"/profile/toggle-notifications",
	authMiddleware,
	async (req, res) => {
		try {
			const userId = req.user.id;
			const user = await User.findById(userId);

			await User.findByIdAndUpdate(userId, {
				emailNotifications: !user.emailNotifications,
			});

			res.redirect("/profile");
		} catch (error) {
			res.status(500).json({
				message: "Error updating notifications",
			});
		}
	}
);

router.use("/auth", authRoutes);
router.use("/post", postRoutes);
router.use("/gallery", galleryRoutes);

export default router;
