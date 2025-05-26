import mongoose from "mongoose";
import { errors } from "../utils/errors.mjs";

const likeSchema = new mongoose.Schema({
	userId: {
		type: mongoose.Schema.Types.ObjectId,
		ref: "User",
		required: true,
	},
	postId: {
		type: mongoose.Schema.Types.ObjectId,
		ref: "Post",
		required: true,
	},
});

likeSchema.pre("save", async function (next) {
	try {
		const existingLike = await Like.findOne({
			userId: this.userId,
			postId: this.postId,
		});
		if (existingLike) {
			const error = new Error(errors.ALREADY_LIKED);
			error.statusCode = 400;
			return next(error);
		}
		next();
	} catch (error) {
		next(error);
	}
});

const Like = mongoose.model("Like", likeSchema);
export default Like;
