export const errors = {
	EMAIL_FORMAT: {
		id: "email",
		message: "Email incorrect format",
	},
	USERNAME_FORMAT: {
		id: "username",
		message: "Username incorrect format",
	},
	PASSWORD_FORMAT: {
		id: "password",
		message: "Password incorrect format",
	},
	EMAIL_IN_USE: { id: "email", message: "Email already in use" },
	USERNAME_IN_USE: { id: "username", message: "Username already in use" },
	USER_NOT_FOUND: { id: "global", message: "User not found" },
	INVALID_PASSWORD: { id: "global", message: "Invalid password" },
	INVALID_CREDENTIALS: {
		id: "global",
		message: "Invalid username or password",
	},
	SAVING_USER: { id: "global", message: "Error saving user" },
	GETTING_POSTS: { id: "global", message: "Error getting posts" },
	PUBLISHING_POST: { id: "global", message: "Error publishing post" },
	IMAGE_REQUIRED: { id: "global", message: "Image is required" },
	VOTING: { id: "global", message: "Error voting this post" },
	NO_POSTS: { id: "global", message: "No posts found" },
};
