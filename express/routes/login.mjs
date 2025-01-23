import express from "express";
import bcrypt from "bcrypt";
import jwt from "jsonwebtoken";
import User from "../models/User.mjs";

const router = express.Router();

router.get("/login", (req, res) => {
	const next = req.query.next || "/profil";
	res.render("login", { next });
});

router.post("/login", async (req, res) => {
	const { username, password, next = "/profil" } = req.body;

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
		{ id: user._id, username: user.username, email: user.email },
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

export default router;
