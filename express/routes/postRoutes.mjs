import express from "express";
import multer from "multer";
import path from "path";
import Post from "../models/Post.mjs";
import { errors } from "../utils/errors.mjs";
import { authMiddleware } from "../middleware/authMiddleware.mjs";

const router = express.Router();

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

router.get("/get-dual", async (req, res) => {
	try {
		const posts = [];
		for (let i = 0; i < req.query.count; i++) {
			const post = await Post.aggregate([
				{ $sample: { size: 2 } },
				{
					$lookup: {
						from: "users",
						localField: "userId",
						foreignField: "_id",
						as: "user",
					},
				},
				{ $unwind: "$user" },
			]);
			posts.push(post);
		}
		res.status(200).json(posts);
	} catch (error) {
		res.status(500).json(errors.GETTING_POSTS.message);
	}
});

router.get("/publish", authMiddleware, (req, res) => {
	res.render("postPublish");
});

router.get("/webcam", authMiddleware, (req, res) => {
	res.render("postWebcam");
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
				return res.status(400).json({ message: "Image file is required" });
			}

			const imageUrl = `/uploads/${imageFile.filename}`;

			const newPost = new Post({
				userId: userId,
				imageUrl: imageUrl,
				createdAt: new Date(),
				votes: 0,
			});

			await newPost.save();

			res
				.status(201)
				.json({ message: "Post created successfully", post: newPost });
		} catch (error) {
			console.error(error);
			res.status(500).json({ message: "Error creating post" });
		}
	}
);

export default router;
