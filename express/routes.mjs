import express from "express";
import User from "./models/User.mjs";

const router = express.Router();

router.get("/", (req, res) => {
	res.render("index", { title: "Accueil" });
});

router.get("/register", (req, res) => {
	res.render("register", { title: "Créer un Compte" });
});

router.post("/register", async (req, res) => {
	const { username, email, password } = req.body;

	const newUser = new User({
		username,
		email,
		password,
	});

	try {
		await newUser.save();
		res.redirect("/");
	} catch (error) {
		console.error("Erreur lors de l'ajout de l'utilisateur:", error);
		res.status(500).send("Erreur lors de l'ajout de l'utilisateur");
	}
});

export default router;
