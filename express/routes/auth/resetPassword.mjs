import express from "express";
import User from "../../models/User.mjs";
import { passwordValidation } from "../../utils/passwordValidation.mjs";

const router = express.Router();

router.get("/reset-password/:token", async (req, res) => {
	const { token } = req.params;
	const user = await User.findOne({ resetPasswordToken: token });
	if (!user) {
		return res.status(404).send("Invalid or expired token.");
	}
	if (user.resetPasswordExpires < Date.now()) {
		return res.status(404).send("Token expired.");
	}
	res.render("resetPassword", { token });
});

router.post("/reset-password/:token", async (req, res) => {
	const { token } = req.params;
	const { password } = req.body;

	try {
		const user = await User.findOne({ resetPasswordToken: token });
		if (!user) {
			return res.status(404).send("Invalid or expired token.");
		}

		if (user.resetPasswordExpires < Date.now()) {
			return res.status(404).send("Token expired.");
		}

		if (!passwordValidation(password)) {
			return res.render("resetPassword", {
				id: "password",
				message: "Password validation error: incorrect format",
			});
		}

		user.password = password;
		user.resetPasswordToken = undefined;
		user.resetPasswordExpires = undefined;
		await user.save();

		res.redirect("/auth/login");
	} catch (error) {
		res.status(500).render("resetPassword", {
			id: "global",
			message: "Error resetting password",
		});
	}
});

export default router;
