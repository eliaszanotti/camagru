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

	user.isEmailVerified = false;
	user.email = email;
	user.emailVerificationToken = crypto.randomBytes(32).toString("hex");
	try {
		await user.save();
		res.redirect("/auth/check-email");
	} catch (error) {
		return res.render("changeEmail", {
			id: "global",
			message: "Error saving user",
		});
	}
});

export default router;
