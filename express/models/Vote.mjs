import mongoose from "mongoose";

const voteSchema = new mongoose.Schema({
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

voteSchema.pre("save", async function (next) {
	try {
		const existingVote = await Vote.findOne({
			userId: this.userId,
			postId: this.postId,
		});
		if (existingVote) {
			// TODO ici renvoie un erreur du json centralisé d'erreur
			const error = new Error("You have already voted for this post");
			error.statusCode = 400;
			return next(error);
		}
		next();
	} catch (error) {
		next(error);
	}
});

const Vote = mongoose.model("Vote", voteSchema);
export default Vote;
