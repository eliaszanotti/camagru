import express from "express";
import User from "./models/User.mjs";
import { emailValidation } from "./utils/emailValidation.mjs";
import { usernameValidation } from "./utils/usernameValidation.mjs";
import { passwordValidation } from "./utils/passwordValidation.mjs";

const router = express.Router();

router.get("/", (req, res) => {
	res.render("index", { title: "Podium" });
});

router.get("/register", (req, res) => {
	res.render("register", { title: "Register" });
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
	});

	try {
		await newUser.save();
		res.send("User added successfully !");
	} catch (error) {
		res.status(500).render("register", {
			id: "global",
			message: "Error during user addition",
		});
	}
});

export default router;
