import express from "express";
import User from "./models/User.mjs";

const router = express.Router();

router.get("/", (req, res) => {
	res.render("index", { title: "Accueil" });
});

router.get("/register", (req, res) => {
	res.render("register", { title: "Créer un Compte" });
});

router.get("/add-user", async (req, res) => {
	const newUser = new User({
		username: "testuser",
		email: "testuser@example.com",
		password: "password123",
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
