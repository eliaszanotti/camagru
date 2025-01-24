import express from "express";
import User from "../models/User.mjs";
import crypto from "crypto";
import transporter from "../utils/emailTransporter.mjs";
import { resetPasswordMailOptions } from "../utils/mailOptions.mjs";

const router = express.Router();

router.get("/forgot-password", (req, res) => {
	res.render("forgotPassword");
});

router.post("/forgot-password", async (req, res) => {
	const { email } = req.body;

	const user = await User.findOne({ email });
	if (!user) {
		return res.status(404).render("forgotPassword", {
			id: "global",
			message: "User not found",
		});
	}

	const resetToken = crypto.randomBytes(32).toString("hex");
	user.resetPasswordToken = resetToken;
	user.resetPasswordExpires = Date.now() + 600000;
	await user.save();

	try {
		const mailOptions = resetPasswordMailOptions(user);
		await transporter.sendMail(mailOptions);
		res.redirect("/auth/check-email-password");
	} catch (error) {
		res.status(500).render("forgotPassword", {
			id: "global",
			message: "Error during password reset",
		});
	}
});

export default router;
