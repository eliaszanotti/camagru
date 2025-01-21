import express from "express";
import User from "./models/User.mjs";
import { emailValidation } from "./utils/emailValidation.mjs";
import { usernameValidation } from "./utils/usernameValidation.mjs";
import { passwordValidation } from "./utils/passwordValidation.mjs";
import SibApiV3Sdk from "sib-api-v3-sdk";

const router = express.Router();

const defaultClient = SibApiV3Sdk.ApiClient.instance;
const apiKey = defaultClient.authentications["api-key"];
apiKey.apiKey = process.env.BREVO_API_KEY;

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

		const apiInstance = new SibApiV3Sdk.TransactionalEmailsApi();
		const sendSmtpEmail = new SibApiV3Sdk.SendSmtpEmail();

		sendSmtpEmail.sender = {
			name: "Podium",
			email: process.env.BREVO_SENDER_EMAIL,
		};
		sendSmtpEmail.to = [{ email: email, name: username }];
		sendSmtpEmail.subject = "Vérification de votre email";
		sendSmtpEmail.htmlContent = `
			<html>
				<body>
					<h1>Veuillez cliquer sur ce lien pour vérifier votre email : 
						<a href="http://localhost:3000/verify-email/${newUser._id}">Vérifier mon email</a>
					</h1>
					<p>Si vous n'avez pas créé de compte, veuillez ignorer ce message.</p>
				</body>
			</html>
		`;

		await apiInstance.sendTransacEmail(sendSmtpEmail);
		res.send(
			"User added successfully! Please check your email for verification."
		);
	} catch (error) {
		res.status(500).render("register", {
			id: "global",
			message: "Error during user addition",
		});
	}
});

export default router;
