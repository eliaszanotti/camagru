import mongoose from "mongoose";

const postSchema = new mongoose.Schema({
	userId: {
		type: mongoose.Schema.Types.ObjectId,
		ref: "User",
		required: true,
	},
	comments: [{ type: mongoose.Schema.Types.ObjectId, ref: "Comment" }],
	likes: [{ type: mongoose.Schema.Types.ObjectId, ref: "Like" }],
	originaImageUrl: { type: String, default: null },
	imageUrl: { type: String, required: true },
	createdAt: { type: Date, default: Date.now },
	isPublished: { type: Boolean, default: false },
});

const Post = mongoose.model("Post", postSchema);
export default Post;
