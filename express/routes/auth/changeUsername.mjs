import express from "express";
import bcrypt from "bcrypt";
import User from "../../models/User.mjs";
import { usernameValidation } from "../../utils/validations.mjs";
import { authMiddleware } from "../../middleware/authMiddleware.mjs";
import { errors } from "../../utils/errors.mjs";

const router = express.Router();

router.get("/change-username", authMiddleware, (req, res) => {
	res.render("authChangeUsername");
});

router.post("/change-username", authMiddleware, async (req, res) => {
	const { username, password } = req.body;

	if (!usernameValidation(username)) {
		return res.render("authChangeUsername", errors.USERNAME_FORMAT);
	}

	const existingUser = await User.findOne({ username });
	if (existingUser) {
		return res.render("authChangeUsername", errors.USERNAME_IN_USE);
	}

	const user = await User.findById(req.user.id);
	if (!user) {
		return res.render("authChangeUsername", errors.USER_NOT_FOUND);
	}

	const isMatch = await bcrypt.compare(password, user.password);
	if (!isMatch) {
		return res.render("authChangeUsername", errors.INVALID_PASSWORD);
	}

	user.username = username;
	try {
		await user.save();
		res.redirect("/profile");
	} catch (error) {
		return res.render("authChangeUsername", errors.SAVING_USER);
	}
});

export default router;
