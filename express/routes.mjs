import express from "express";
import User from "./models/User.mjs";
import { passwordValidation } from "./utils/passwordValidation.mjs";

const router = express.Router();

router.get("/", (req, res) => {
	res.render("index", { title: "Accueil" });
});

router.get("/register", (req, res) => {
	res.render("register", { title: "Créer un Compte" });
});

router.post("/register", async (req, res) => {
	const { username, email, password } = req.body;

	if (!passwordValidation(password)) {
		return res
			.status(400)
			.send(
				"Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial."
			);
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
