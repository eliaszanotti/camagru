import express from "express";
import User from "../../models/User.mjs";
import { verifyMailOptions } from "../../utils/mailOptions.mjs";
import transporter from "../../utils/emailTransporter.mjs";
import crypto from "crypto";

const router = express.Router();

router.get("/verify-email/:token", async (req, res) => {
	const { token } = req.params;

	try {
		const user = await User.findOne({ emailVerificationToken: token });

		if (!user) {
			return res.status(404).send("Invalid or expired token.");
		}

		user.isEmailVerified = true;
		user.emailVerificationToken = undefined;
		await user.save();

		res.redirect("/profile");
	} catch (error) {
		res.status(500).send("Error verifying email.");
	}
});

router.get("/verify-email", async (req, res) => {
	if (!req.user) {
		return res.redirect("/auth/login");
	}

	const userId = req.user.id;
	const user = await User.findById(userId);
	try {
		if (!user) {
			return res.redirect("/auth/login");
		}
		user.emailVerificationToken = crypto.randomBytes(32).toString("hex");
		user.save();
	} catch {
		res.status(500).send("Error verifying email.");
	}

	try {
		const mailOptions = verifyMailOptions(user);
		await transporter.sendMail(mailOptions);
		res.redirect("/auth/check-email");
	} catch (emailError) {
		res.redirect("/profile");
	}
});

export default router;
