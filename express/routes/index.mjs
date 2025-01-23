import express from "express";
import authRoutes from "./authRoutes.mjs";
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

export default router;
