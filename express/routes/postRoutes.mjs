import express from "express";
import Post from "../models/Post.mjs";
import { errors } from "../utils/errors.mjs";
import { authMiddleware } from "../middleware/authMiddleware.mjs";

const router = express.Router();

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
		res.status(500).json(errors.GETTING_POSTS);
		res.status(200).json(posts);
	} catch (error) {
		res.status(500).json(errors.GETTING_POSTS);
	}
});

router.get("/webcam", authMiddleware, (req, res) => {
	res.render("postWebcam");
});

export default router;
