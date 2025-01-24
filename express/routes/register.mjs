import express from "express";
import crypto from "crypto";
import User from "../models/User.mjs";
import { emailValidation } from "../utils/emailValidation.mjs";
import { usernameValidation } from "../utils/usernameValidation.mjs";
import { passwordValidation } from "../utils/passwordValidation.mjs";
import { verifyMailOptions } from "../utils/verifyMailOptions.mjs";
import transporter from "../utils/emailTransporter.mjs";

const router = express.Router();

router.get("/register", (req, res) => {
	res.render("register");
});

const validateInput = (username, email, password) => {
	if (!emailValidation(email)) {
		return {
			valid: false,
			id: "email",
			message: "Email validation error: incorrect format",
		};
	}
	if (!usernameValidation(username)) {
		return {
			valid: false,
			id: "username",
			message: "Username validation error: incorrect format",
		};
	}
	if (!passwordValidation(password)) {
		return {
			valid: false,
			id: "password",
			message: "Password validation error: incorrect format",
		};
	}
	return { valid: true };
};

const checkExistingUser = async (username, email) => {
	const existingEmail = await User.findOne({ email });
	if (existingEmail) {
		return { valid: false, id: "email", message: "Email already in use" };
	}

	const existingUsername = await User.findOne({ username });
	if (existingUsername) {
		return { valid: false, id: "username", message: "Username already in use" };
	}

	return { valid: true };
};

router.post("/register", async (req, res) => {
	const { username, email, password } = req.body;

	const validation = validateInput(username, email, password);
	if (!validation.valid) {
		return res.render("register", {
			id: validation.id,
			message: validation.message,
		});
	}

	const existingUser = await checkExistingUser(username, email);
	if (!existingUser.valid) {
		return res.render("register", {
			id: existingUser.id,
			message: existingUser.message,
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

		const mailOptions = verifyMailOptions(newUser);
		await transporter.sendMail(mailOptions);
		res.redirect("/auth/check-email");
	} catch (error) {
		res.status(500).render("register", {
			id: "global",
			message: "Error during user addition",
		});
	}
});

export default router;
