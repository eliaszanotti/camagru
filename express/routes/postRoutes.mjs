import express from "express";
import Post from "../models/Post.mjs";
import Vote from "../models/Vote.mjs";
import { errors } from "../utils/errors.mjs";
import { authMiddleware } from "../middleware/authMiddleware.mjs";
import postPublishRoute from "./postPublish.mjs";

const router = express.Router();
router.use(express.json());
router.use(postPublishRoute);

router.get("/get-dual", async (req, res) => {
	try {
		const posts = await Post.aggregate([
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
			{
				$lookup: {
					from: "votes",
					localField: "_id",
					foreignField: "postId",
					as: "votes",
				},
			},
			{
				$addFields: {
					voteCount: { $size: "$votes" },
				},
			},
			{
				$project: {
					votes: 0,
				},
			},
		]);
		console.log(posts);
		res.render("postDualSection", { posts });
	} catch (error) {
		res.status(500).json(errors.GETTING_POSTS);
	}
});

router.get("/webcam", authMiddleware, (req, res) => {
	res.render("postWebcam");
});

router.post("/vote", authMiddleware, async (req, res) => {
	const { postId } = req.body;
	const userId = req.user.id;

	try {
		const vote = new Vote({ userId, postId });
		await vote.save();
		res.status(200).json({ success: true });
	} catch (error) {
		res.status(500).json(errors.VOTING);
	}
});

export default router;
