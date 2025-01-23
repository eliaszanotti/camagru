import express from "express";
import User from "../models/User.mjs";
import { passwordValidation } from "../utils/passwordValidation.mjs";
import registerRoute from "./auth/register.mjs";
import forgotPasswordRoute from "./auth/forgotPassword.mjs";
import loginRoute from "./auth/login.mjs";

const router = express.Router();
router.use(registerRoute);
router.use(forgotPasswordRoute);
router.use(loginRoute);

router.get("/check-email", (req, res) => {
	res.render("checkEmail");
});

router.get("/logout", (req, res) => {
	res.clearCookie("token");
	res.redirect("/auth/login");
});

router.get("/check-email-password", (req, res) => {
	res.render("checkEmailPassword");
});

router.get("/reset-password/:token", async (req, res) => {
	const { token } = req.params;
	const user = await User.findOne({ resetPasswordToken: token });
	if (!user) {
		return res.status(404).send("Invalid or expired token.");
	}
	if (user.resetPasswordExpires < Date.now()) {
		return res.status(404).send("Token expired.");
	}
	res.render("resetPassword", { token });
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

		res.redirect("/auth/login");
	} catch (error) {
		res.status(500).send("Error verifying email.");
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

		if (!passwordValidation(password)) {
			return res.render("resetPassword", {
				id: "password",
				message: "Password validation error: incorrect format",
			});
		}

		user.password = password;
		user.resetPasswordToken = undefined;
		user.resetPasswordExpires = undefined;
		await user.save();

		res.redirect("/auth/login");
	} catch (error) {
		res.status(500).render("resetPassword", {
			id: "global",
			message: "Error resetting password",
		});
	}
});

export default router;
