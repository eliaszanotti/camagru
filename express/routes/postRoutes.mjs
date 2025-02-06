import express from "express";
import Post from "../models/Post.mjs";
import { errors } from "../utils/errors.mjs";
import { authMiddleware } from "../middleware/authMiddleware.mjs";

const router = express.Router();

router.get("/get-dual", async (req, res) => {
	try {
		const posts = await Post.aggregate([{ $sample: { size: 2 } }]);
		res.status(200).json(posts);
	} catch (error) {
		res.status(500).json(errors.GETTING_POSTS.message);
	}
});

router.get("/publish", authMiddleware, (req, res) => {
	res.render("publishPost");
});

export default router;
