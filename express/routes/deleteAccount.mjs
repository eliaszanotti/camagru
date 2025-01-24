import express from "express";
import User from "../models/User.mjs";
import bcrypt from "bcrypt";
import { authMiddleware } from "../middleware/authMiddleware.mjs";
import { errors } from "../utils/errors.mjs";

const router = express.Router();

router.get("/delete-account", authMiddleware, (req, res) => {
	res.render("deleteAccount");
});

router.post("/delete-account", authMiddleware, async (req, res) => {
	const { password } = req.body;

	const user = await User.findById(req.user.id);
	if (!user) {
		return res.render("deleteAccount", errors.USER_NOT_FOUND);
	}

	const isMatch = await bcrypt.compare(password, user.password);
	if (!isMatch) {
		return res.render("deleteAccount", errors.INVALID_PASSWORD);
	}

	await User.findByIdAndDelete(req.user.id);
	res.redirect("/auth/logout");
});

export default router;
