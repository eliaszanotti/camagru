export const usernameValidation = (username) => {
	if (username.length < 4 || username.length > 16) {
		return false;
	}
	const usernameRegex = /^[a-zA-Z0-9]+$|^(?!.*\s).*$/;
	return usernameRegex.test(username);
};
