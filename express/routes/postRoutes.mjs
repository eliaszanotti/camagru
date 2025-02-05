import express from "express";
import Post from "../models/Post.mjs";
import { errors } from "../utils/errors.mjs";

const router = express.Router();

router.get("/get-dual", async (req, res) => {
	try {
		const posts = await Post.aggregate([{ $sample: { size: 2 } }]);
		res.status(200).json(posts);
	} catch (error) {
		res.status(500).json(errors.GETTING_POSTS.message);
	}
});

export default router;
