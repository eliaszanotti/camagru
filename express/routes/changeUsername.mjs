import express from "express";
import bcrypt from "bcrypt";
import User from "../models/User.mjs";
import { usernameValidation } from "../utils/validations.mjs";
import { authMiddleware } from "../middleware/authMiddleware.mjs";
import { errors } from "../utils/errors.mjs";

const router = express.Router();

router.get("/change-username", authMiddleware, (req, res) => {
	res.render("changeUsername");
});

router.post("/change-username", authMiddleware, async (req, res) => {
	const { username, password } = req.body;

	if (!usernameValidation(username)) {
		return res.render("changeUsername", errors.USERNAME_FORMAT);
	}

	const existingUser = await User.findOne({ username });
	if (existingUser) {
		return res.render("changeUsername", errors.USERNAME_IN_USE);
	}

	const user = await User.findById(req.user.id);
	if (!user) {
		return res.render("changeUsername", errors.USER_NOT_FOUND);
	}

	const isMatch = await bcrypt.compare(password, user.password);
	if (!isMatch) {
		return res.render("changeUsername", errors.INVALID_PASSWORD);
	}

	user.username = username;
	try {
		await user.save();
		res.redirect("/profil");
	} catch (error) {
		return res.render("changeUsername", errors.SAVING_USER);
	}
});

export default router;
