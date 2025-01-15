const express = require("express");
const path = require("path");
const mongoose = require("mongoose");
const User = require("./models/User");

const app = express();
const PORT = process.env.PORT || 3000;

const mongoURI = process.env.MONGO_URI;

mongoose
	.connect(mongoURI, { useNewUrlParser: true, useUnifiedTopology: true })
	.then(() => console.log("Connecté à MongoDB"))
	.catch((err) => console.error("Erreur de connexion à MongoDB:", err));

app.set("views", path.join(__dirname, "views"));
app.set("view engine", "ejs");

app.use(express.static(path.join(__dirname, "public")));
app.use(express.urlencoded({ extended: true }));

app.get("/", (req, res) => {
	res.render("index", { title: "Accueil" });
});

app.get("/register", (req, res) => {
	res.render("register", { title: "Créer un Compte" });
});

app.get("/add-user", async (req, res) => {
	const newUser = new User({
		username: "testuser",
		email: "testuser@example.com",
		password: "password123"
	});

	try {
		await newUser.save();
		res.send("Utilisateur ajouté avec succès !");
	} catch (error) {
		console.error("Erreur lors de l'ajout de l'utilisateur:", error);
		res.status(500).send("Erreur lors de l'ajout de l'utilisateur");
	}
});

app.listen(PORT, () => {
	console.log(`Server is running on http://localhost:${PORT}`);
});
