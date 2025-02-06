import express from "express";
import User from "../models/User.mjs";
import crypto from "crypto";
import transporter from "../utils/emailTransporter.mjs";
import { resetPasswordMailOptions } from "../utils/mailOptions.mjs";
import { errors } from "../utils/errors.mjs";

const router = express.Router();

router.get("/forgot-password", (req, res) => {
	res.render("authForgotPassword");
});

router.post("/forgot-password", async (req, res) => {
	const { email } = req.body;

	const user = await User.findOne({ email });
	if (!user) {
		return res.status(404).render("authForgotPassword", errors.USER_NOT_FOUND);
	}

	const resetToken = crypto.randomBytes(32).toString("hex");
	user.resetPasswordToken = resetToken;
	user.resetPasswordExpires = Date.now() + 600000;

	try {
		await user.save();
		const mailOptions = resetPasswordMailOptions(user);
		await transporter.sendMail(mailOptions);
		res.redirect("/auth/check-email-password");
	} catch (error) {
		res.status(500).render("authForgotPassword", errors.SAVING_USER);
	}
});

export default router;
