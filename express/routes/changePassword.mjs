import express from "express";
import bcrypt from "bcrypt";
import User from "../models/User.mjs";
import { passwordValidation } from "../utils/passwordValidation.mjs";
import { authMiddleware } from "../middleware/authMiddleware.mjs";

const router = express.Router();

router.get("/change-password", authMiddleware, (req, res) => {
	res.render("changePassword");
});

router.post("/change-password", authMiddleware, async (req, res) => {
	const { password, newPassword } = req.body;

	if (!passwordValidation(newPassword)) {
		return res.render("changePassword", {
			id: "password",
			message: "Invalid password",
		});
	}

	const user = await User.findById(req.user.id);
	if (!user) {
		return res.render("changePassword", {
			id: "global",
			message: "User not found",
		});
	}

	const isMatch = await bcrypt.compare(password, user.password);
	if (!isMatch) {
		return res.render("changePassword", {
			id: "global",
			message: "Invalid password",
		});
	}

	user.password = newPassword;
	try {
		await user.save();
		res.redirect("/profil");
	} catch (error) {
		return res.render("changePassword", {
			id: "global",
			message: "Error during password change",
		});
	}
});

export default router;
