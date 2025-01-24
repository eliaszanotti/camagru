export const emailValidation = (email) => {
	const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
	return emailRegex.test(email);
};

export const usernameValidation = (username) => {
	if (username.length < 4 || username.length > 16) {
		return false;
	}
	const usernameRegex = /^[a-zA-Z0-9]+$|^(?!.*\s).*$/;
	return usernameRegex.test(username);
};

export const passwordValidation = (password) => {
	const passwordRegex = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*]).{8,}$/;
	return passwordRegex.test(password);
};
