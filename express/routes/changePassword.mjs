import express from "express";
import bcrypt from "bcrypt";
import User from "../models/User.mjs";
import { passwordValidation } from "../utils/validations.mjs";
import { authMiddleware } from "../middleware/authMiddleware.mjs";
import { errors } from "../utils/errors.mjs";

const router = express.Router();

router.get("/change-password", authMiddleware, (req, res) => {
	res.render("changePassword");
});

router.post("/change-password", authMiddleware, async (req, res) => {
	const { password, newPassword } = req.body;

	if (!passwordValidation(newPassword)) {
		return res.render("changePassword", errors.PASSWORD_FORMAT);
	}

	const user = await User.findById(req.user.id);
	if (!user) {
		return res.render("changePassword", errors.USER_NOT_FOUND);
	}

	const isMatch = await bcrypt.compare(password, user.password);
	if (!isMatch) {
		return res.render("changePassword", errors.INVALID_PASSWORD);
	}

	user.password = newPassword;
	try {
		await user.save();
		res.redirect("/profil");
	} catch (error) {
		return res.render("changePassword", errors.SAVING_USER);
	}
});

export default router;
