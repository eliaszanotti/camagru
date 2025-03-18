import express from "express";
import Post from "../models/Post.mjs";
import User from "../models/User.mjs";
import Vote from "../models/Vote.mjs";
import { errors } from "../utils/errors.mjs";
import { authMiddleware } from "../middleware/authMiddleware.mjs";
import postPublishRoute from "./postPublish.mjs";

const router = express.Router();
router.use(express.json());
router.use(postPublishRoute);

router.get("/random", async (req, res) => {
	try {
		const posts = await Post.aggregate([{ $sample: { size: 1 } }]);
		if (posts.length === 0) {
			return res.status(404).json(errors.NO_POSTS);
		}
		const user = await User.findById(posts[0].userId);
		res.render("includes/postSingle", { post: posts[0], user: user });
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
		// TODO ici prend lerreur du Vote.save dans Vote.mjs
		res.status(500).json(errors.VOTING);
	}
});

export default router;
