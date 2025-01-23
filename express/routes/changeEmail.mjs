import express from "express";
import crypto from "crypto";
import nodemailer from "nodemailer";
import bcrypt from "bcrypt";
import User from "../models/User.mjs";
import { emailValidation } from "../utils/emailValidation.mjs";
import { usernameValidation } from "../utils/usernameValidation.mjs";
import { passwordValidation } from "../utils/passwordValidation.mjs";
import { authMiddleware } from "../middleware/authMiddleware.mjs";

const router = express.Router();

router.get("/change-email", authMiddleware, (req, res) => {
	res.render("changeEmail");
});

const transporter = nodemailer.createTransport({
	service: "gmail",
	auth: {
		user: process.env.GOOGLE_USER,
		pass: process.env.GOOGLE_APP_PASSWORD,
	},
});

const getMailOptions = (user) => {
	return {
		from: process.env.GOOGLE_USER,
		to: user.email,
		subject: "Verification of your email",
		html: `
		<h1>Hi ${user.username}, welcome to Podium !</h1>
		<p>Please click on this link to verify your email: 
			<a href="${process.env.SERVER_URL}/auth/verify-email/${user.emailVerificationToken}">
			Verify my email</a>
		</p>`,
	};
};

router.post("/change-email", authMiddleware, async (req, res) => {
	const { email, password } = req.body;

	if (!emailValidation(email)) {
		return res.render("changeEmail", {
			id: "email",
			message: "Invalid email",
		});
	}

	const user = await User.findById(req.user.id);
	if (!user) {
		return res.render("changeEmail", {
			id: "global",
			message: "User not found",
		});
	}

	const isMatch = await bcrypt.compare(password, user.password);
	if (!isMatch) {
		return res.render("changeEmail", {
			id: "global",
			message: "Invalid password",
		});
	}

	// TODO DRY this with register.mjs
	user.isEmailVerified = false;
	user.email = email;
	user.emailVerificationToken = crypto.randomBytes(32).toString("hex");
	try {
		await user.save();

		const mailOptions = getMailOptions(user);
		await transporter.sendMail(mailOptions);
		res.redirect("/auth/check-email");
	} catch (error) {
		return res.render("changeEmail", {
			id: "global",
			message: "Error saving user",
		});
	}
});

export default router;
