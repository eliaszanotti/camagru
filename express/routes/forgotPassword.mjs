import express from "express";
import User from "../models/User.mjs";
import crypto from "crypto";
import nodemailer from "nodemailer";

const router = express.Router();

// TODO voir pour pas repeter ce code avec register.mjs
const transporter = nodemailer.createTransport({
	service: "gmail",
	auth: {
		user: process.env.GOOGLE_USER,
		pass: process.env.GOOGLE_APP_PASSWORD,
	},
});

router.get("/forgot-password", (req, res) => {
	res.render("forgotPassword");
});

const getMailOptions = (user) => {
	return {
		from: process.env.GOOGLE_USER,
		to: user.email,
		subject: "Reset your password",
		html: `
		<h1>Hi ${user.username},</h1>
		<p>Please click on this link to reset your password: 
			<a href="${process.env.SERVER_URL}/auth/reset-password/${user.resetPasswordToken}">
			Reset my password</a>
		</p>
		<p>This link will expire in 10 minutes.</p>`,
	};
};

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
		const mailOptions = getMailOptions(user);
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
