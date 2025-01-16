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
			message: "Email validation error: incorect format",
		});
	}

	if (!usernameValidation(username)) {
		return res.render("register", {
			id: "username",
			message: "Username validation error: incorect format",
		});
	}

	if (!passwordValidation(password)) {
		return res.render("register", {
			id: "password",
			message: "Password validation error: incorect format",
		});
	}

	const newUser = new User({
		username,
		email,
		password,
	});

	try {
		await newUser.save();
		res.send("Utilisateur ajouté avec succès !");
	} catch (error) {
		console.error("Erreur lors de l'ajout de l'utilisateur:", error);
		res.status(500).send("Erreur lors de l'ajout de l'utilisateur");
	}
});

export default router;
