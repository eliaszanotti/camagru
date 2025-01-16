import express from "express";
import User from "./models/User.mjs";
import { passwordValidation } from "./utils/passwordValidation.mjs";
import { usernameValidation } from "./utils/usernameValidation.mjs";

const router = express.Router();

router.get("/", (req, res) => {
	res.render("index", { title: "Podium" });
});

router.get("/register", (req, res) => {
	res.render("register", { title: "Register" });
});

router.post("/register", async (req, res) => {
	const { username, email, password } = req.body;

	if (!passwordValidation(password)) {
		return res.render("register", {
			field: "password",
			error:
				"Password must be more than 8 characters long, including at least one uppercase letter, one lowercase letter, one number, and one special character.",
		});
	}

	if (!usernameValidation(username)) {
		return res.render("register", {
			field: "username",
			error:
				"Username must contain only letters, numbers, and hyphens, and must be between 4 and 16 characters",
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
