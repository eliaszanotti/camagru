import express from "express";
import User from "../models/User.mjs";
import registerRoute from "./auth/register.mjs";
import forgotPasswordRoute from "./auth/forgotPassword.mjs";
import loginRoute from "./auth/login.mjs";
import resetPasswordRoute from "./auth/resetPassword.mjs";

const router = express.Router();
router.use(registerRoute);
router.use(forgotPasswordRoute);
router.use(loginRoute);
router.use(resetPasswordRoute);

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

export default router;
