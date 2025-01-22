import express from "express";
import nodemailer from "nodemailer";
import crypto from "crypto";
import User from "../models/User.mjs";
import { emailValidation } from "../utils/emailValidation.mjs";
import { usernameValidation } from "../utils/usernameValidation.mjs";
import { passwordValidation } from "../utils/passwordValidation.mjs";

const router = express.Router();

const transporter = nodemailer.createTransport({
	service: "gmail",
	auth: {
		user: process.env.GOOGLE_USER,
		pass: process.env.GOOGLE_APP_PASSWORD,
	},
});

router.get("/register", (req, res) => {
	res.render("register", { title: "Register" });
});

router.get("/check-email", (req, res) => {
	res.render("checkEmail", { title: "Check Your Email" });
});

router.post("/register", async (req, res) => {
	const { username, email, password } = req.body;

	if (!emailValidation(email)) {
		return res.render("register", {
			id: "email",
			message: "Email validation error: incorrect format",
		});
	}

	if (!usernameValidation(username)) {
		return res.render("register", {
			id: "username",
			message: "Username validation error: incorrect format",
		});
	}

	if (!passwordValidation(password)) {
		return res.render("register", {
			id: "password",
			message: "Password validation error: incorrect format",
		});
	}

	const existingEmail = await User.findOne({ email });
	if (existingEmail) {
		return res.render("register", {
			id: "email",
			message: "Email already in use",
		});
	}

	const existingUsername = await User.findOne({ username });
	if (existingUsername) {
		return res.render("register", {
			id: "username",
			message: "Username already in use",
		});
	}

	const newUser = new User({
		username,
		email,
		password,
		emailVerificationToken: crypto.randomBytes(32).toString("hex"),
	});

	try {
		await newUser.save();

		const mailOptions = {
			from: process.env.GOOGLE_USER,
			to: email,
			subject: "Vérification de votre email",
			html: `
			<h1>Hi ${username}, welcome to Podium !</h1>
			<p>Please click on this link to verify your email: 
				<a href="${process.env.SERVER_URL}/auth/verify-email/${newUser.emailVerificationToken}">
				Verify my email</a>
			</p>`,
		};

		await transporter.sendMail(mailOptions);
		res.send(
			"User added successfully! Please check your email for verification."
		);
	} catch (error) {
		res.status(500).render("register", {
			id: "global",
			message: "Error during user addition",
		});
	}
});

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

		res.send("Email successfully verified! You can now log in.");
		// TODO add a page to redirect to
	} catch (error) {
		console.error("Error verifying email:", error);
		res.status(500).send("Error verifying email.");
	}
});

export default router;
