import express from "express";
import nodemailer from "nodemailer";
import User from "../models/User.mjs";
import { emailValidation } from "../utils/emailValidation.mjs";
import { usernameValidation } from "../utils/usernameValidation.mjs";
import { passwordValidation } from "../utils/passwordValidation.mjs";

const router = express.Router();

router.get("/register", (req, res) => {
	res.render("register", { title: "Register" });
});

export default router;
