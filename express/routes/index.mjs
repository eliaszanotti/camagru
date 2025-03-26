import express from "express";
import authRoutes from "./auth/index.mjs";
import postRoutes from "./post/index.mjs";
import galleryRoutes from "./gallery/index.mjs";
import { authMiddleware } from "../middleware/authMiddleware.mjs";
import User from "../models/User.mjs";

const router = express.Router();

router.get("/", (req, res) => {
	res.render("index", { title: "Podium" });
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
