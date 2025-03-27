import express from "express";
import multer from "multer";
import path from "path";
import Post from "../../models/Post.mjs";
import User from "../../models/User.mjs";
import { errors } from "../../utils/errors.mjs";
import { authMiddleware } from "../../middleware/authMiddleware.mjs";

const storage = multer.diskStorage({
	destination: function (req, file, cb) {
		cb(null, "uploads/");
	},
	filename: function (req, file, cb) {
		const uniqueSuffix = Date.now() + "-" + Math.round(Math.random() * 1e9);
		cb(null, uniqueSuffix + path.extname(file.originalname));
	},
});

const upload = multer({ storage: storage });
const router = express.Router();

router.get("/add", authMiddleware, (req, res) => {
	res.render("galleryAdd");
});

router.post(
	"/upload",
	authMiddleware,
	upload.single("imageFile"),
	async (req, res) => {
		try {
			const userId = req.user.id;
			const imageFile = req.file;

			if (!imageFile) {
				return res.status(400).json(errors.IMAGE_REQUIRED);
			}

			const imageUrl = `/uploads/${imageFile.filename}`;

			const newPost = new Post({
				userId: userId,
				imageUrl: imageUrl,
				createdAt: new Date(),
			});

			await newPost.save();
			res.redirect("/gallery");
		} catch (error) {
			console.error(error);
			res.status(500).json(errors.PUBLISHING_POST);
		}
	}
);

router.get("/webcam", authMiddleware, (req, res) => {
	res.render("galleryWebcam");
});

router.get("/import", authMiddleware, (req, res) => {
	res.render("galleryImport");
});

router.get("/edit/:postId", authMiddleware, async (req, res) => {
	const post = await Post.findById(req.params.postId);
	const user = await User.findById(post.userId);
	res.render("galleryEdit", { post, user });
});

router.get("/", authMiddleware, async (req, res) => {
	try {
		const posts = await Post.find({ userId: req.user.id })
			.populate("userId")
			.sort({ createdAt: -1 });
		res.render("gallery", {
			posts: posts,
		});
	} catch (error) {
		res.status(500).json(errors.GETTING_POSTS);
	}
});

export default router;
