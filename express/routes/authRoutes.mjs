import express from "express";
import nodemailer from "nodemailer";
import crypto from "crypto";
import bcrypt from "bcrypt";
import jwt from "jsonwebtoken";
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
	res.render("register");
});

router.get("/check-email", (req, res) => {
	res.render("checkEmail");
});

router.get("/login", (req, res) => {
	const next = req.query.next || "/profil";
	res.render("login", { next });
});

router.get("/logout", (req, res) => {
	res.clearCookie("token");
	res.redirect("/auth/login");
});

router.get("/forgot-password", (req, res) => {
	res.render("forgotPassword");
});

router.get("/check-email-password", (req, res) => {
	res.render("checkEmailPassword");
});

router.get("/reset-password/:token", (req, res) => {
	const { token } = req.params;
	res.render("resetPassword", { token });
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
			subject: "Verification of your email",
			html: `
			<h1>Hi ${username}, welcome to Podium !</h1>
			<p>Please click on this link to verify your email: 
				<a href="${process.env.SERVER_URL}/auth/verify-email/${newUser.emailVerificationToken}">
				Verify my email</a>
			</p>`,
		};

		await transporter.sendMail(mailOptions);
		res.redirect("/auth/check-email");
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

router.post("/login", async (req, res) => {
	const { username, password, next } = req.body;

	const user = await User.findOne({ $or: [{ username }, { email: username }] });
	if (!user) {
		return res.status(401).render("login", {
			id: "global",
			message: "Username or password incorrect",
		});
	}

	const isMatch = await bcrypt.compare(password, user.password);
	if (!isMatch) {
		return res.status(401).render("login", {
			id: "global",
			message: "Username or password incorrect",
		});
	}

	const token = jwt.sign(
		{ id: user._id, username: user.username },
		process.env.JWT_SECRET,
		{
			expiresIn: "1h",
		}
	);

	res.cookie("token", token, {
		httpOnly: true,
		secure: process.env.NODE_ENV === "production", // Use secure if in production
		sameSite: "Strict",
		maxAge: 3600000, // 1 Hour
	});

	res.redirect(next);
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

	const mailOptions = {
		from: process.env.GOOGLE_USER,
		to: user.email,
		subject: "Reset your password",
		html: `
		<h1>Hi ${user.username},</h1>
		<p>Please click on this link to reset your password: 
			<a href="${process.env.SERVER_URL}/auth/reset-password/${resetToken}">
			Reset my password</a>
		</p>
		<p>This link will expire in 10 minutes.</p>`,
	};

	try {
		await transporter.sendMail(mailOptions);
		res.redirect("/auth/check-email-password");
	} catch (error) {
		res.status(500).render("forgotPassword", {
			id: "global",
			message: "Error during password reset",
		});
	}
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

		user.password = password;
		user.resetPasswordToken = undefined;
		user.resetPasswordExpires = undefined;
		await user.save();

		res.redirect("/auth/login");
	} catch (error) {
		console.error("Error resetting password:", error);
		res.status(500).send("Error resetting password.");
	}
});

export default router;
