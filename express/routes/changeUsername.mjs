import express from "express";
import bcrypt from "bcrypt";
import User from "../models/User.mjs";
import { usernameValidation } from "../utils/usernameValidation.mjs";
import { authMiddleware } from "../middleware/authMiddleware.mjs";

const router = express.Router();

router.get("/change-username", authMiddleware, (req, res) => {
	res.render("changeUsername");
});

router.post("/change-username", authMiddleware, async (req, res) => {
	const { username, password } = req.body;

	if (!usernameValidation(username)) {
		return res.render("changeUsername", {
			id: "username",
			message: "Invalid username",
		});
	}

	const existingUser = await User.findOne({ username });
	if (existingUser) {
		return res.render("changeUsername", {
			id: "username",
			message: "Username already in use",
		});
	}

	const user = await User.findById(req.user.id);
	if (!user) {
		return res.render("changeUsername", {
			id: "global",
			message: "User not found",
		});
	}

	const isMatch = await bcrypt.compare(password, user.password);
	if (!isMatch) {
		return res.render("changeUsername", {
			id: "global",
			message: "Invalid password",
		});
	}

	user.username = username;
	try {
		await user.save();
		res.redirect("/profil");
	} catch (error) {
		return res.render("changeUsername", {
			id: "global",
			message: "Error during username change",
		});
	}
});

export default router;
