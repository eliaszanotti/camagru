import express from "express";
import crypto from "crypto";
import User from "../models/User.mjs";
import { emailValidation, usernameValidation, passwordValidation } from "../utils/validations.mjs";
import { verifyMailOptions } from "../utils/mailOptions.mjs";
import transporter from "../utils/emailTransporter.mjs";
import { errors } from "../utils/errors.mjs";

const router = express.Router();

router.get("/register", (req, res) => {
	res.render("register");
});

router.post("/register", async (req, res) => {
	const { username, email, password } = req.body;

	if (!emailValidation(email)) {
		return res.render("register", errors.EMAIL_FORMAT);
	}

	if (!usernameValidation(username)) {
		return res.render("register", errors.USERNAME_FORMAT);
	}

	if (!passwordValidation(password)) {
		return res.render("register", errors.PASSWORD_FORMAT);
	}

	const existingEmail = await User.findOne({ email });
	if (existingEmail) {
		return res.render("register", errors.EMAIL_IN_USE);
	}

	const existingUsername = await User.findOne({ username });
	if (existingUsername) {
		return res.render("register", errors.USERNAME_IN_USE);
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
		res.status(500).render("register", errors.SAVING_USER);
	}
});

export default router;
