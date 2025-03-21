import express from "express";
import multer from "multer";
import path from "path";
import Post from "../models/Post.mjs";
import { errors } from "../utils/errors.mjs";
import { authMiddleware } from "../middleware/authMiddleware.mjs";

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

router.get("/publish", authMiddleware, (req, res) => {
	res.render("postPublish");
});

router.post(
	"/publish",
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

			// TODO redirect to a page
			res.status(201).json({
				message: "Post created successfully",
				post: newPost,
			});
		} catch (error) {
			console.error(error);
			res.status(500).json(errors.PUBLISHING_POST);
		}
	}
);

export default router;
