import mongoose from "mongoose";

const postSchema = new mongoose.Schema({
	userId: { type: mongoose.Schema.Types.ObjectId, ref: "User", required: true },
	imageUrl: { type: String, required: true },
	createdAt: { type: Date, default: Date.now },
	votes: { type: Number, default: 0 },
});

const Post = mongoose.model("Post", postSchema);
export default Post;
