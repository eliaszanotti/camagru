import mongoose from "mongoose";
import bcrypt from "bcrypt";

const userSchema = new mongoose.Schema({
	username: { type: String, required: true, unique: true },
	email: { type: String, required: true, unique: true },
	password: { type: String, required: true },
	isEmailVerified: { type: Boolean, default: false },
	emailVerificationToken: { type: String, default: null },
});

userSchema.pre("save", async function (next) {
	try {
		if (this.isModified("password")) {
			const salt = await bcrypt.genSalt(10);
			this.password = await bcrypt.hash(this.password, salt);
		}
		next();
	} catch (error) {
		next(error);
	}
});

const User = mongoose.model("User", userSchema);
export default User;
