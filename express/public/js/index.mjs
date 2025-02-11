function likeSvgIcon() {
	const voteButtonHtml = `
		<svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M0 0h24v24H0z" stroke="none"/><path d="M19.5 12.57 12 20l-7.5-7.43A5 5 0 1 1 12 6.01a5 5 0 1 1 7.5 6.57"/></svg>
	`;
	return voteButtonHtml;
}

function createSinglePostHtml(post) {
	const singlePostHtml = `
			<div class="flex flex-col gap-10">
				<div class="flex gap-2 items-center justify-between">
					<h1 class="font-bold">${post.user.username}</h1>
					<p class="text-sm text-gray-500">${new Date(post.createdAt).toLocaleDateString(
						"fr-FR",
						{
							year: "numeric",
							month: "long",
							day: "numeric",
							hour: "numeric",
							minute: "numeric",
							hour12: false,
						}
					)}</p>
				</div>
				<div class="relative rounded-field overflow-hidden">
					<img src="${
						post.imageUrl
					}" alt="Post Image" class="w-full aspect-square object-cover select-none" />
					<div class="absolute inset-0 opacity-0 hover:opacity-100 bg-success/50 flex items-center justify-center transition-opacity duration-300">
						${likeSvgIcon()}
					</div>
				</div>
			</div>
		`;
	return singlePostHtml;
}

function getVoteScoreHtml(posts, index) {
	let classColor = "";
	if (posts[0].votes > posts[1].votes) {
		classColor = index === 0 ? "text-success" : "text-error";
	} else if (posts[0].votes < posts[1].votes) {
		classColor = index === 0 ? "text-error" : "text-success";
	} else {
		classColor = "text-gray-500";
	}

	const textHtml = `
		<p class="font-bold text-xl select-none ${classColor}">${posts[index].votes}</p>
	`;
	return textHtml;
}

function calculateProgressWidth(posts, index) {
	const totalVotes = posts[0].votes + posts[1].votes;
	if (totalVotes === 0) return 0;
	if (index === 0) {
		return (posts[0].votes / totalVotes) * 100;
	} else {
		return (posts[1].votes / totalVotes) * 100;
	}
}

function createProgressBarHtml(posts) {
	const progressBarHtml = `
		<div class="grid grid-cols-2 w-2/5 bg-base-300 rounded-full overflow-hidden">
			<div class="flex justify-self-end h-4 bg-gray-500" style="width: ${calculateProgressWidth(
				posts,
				0
			)}%"></div>
			<div class="h-4 bg-gray-500" style="width: ${calculateProgressWidth(
				posts,
				1
			)}%"></div>
		</div>
	`;
	return progressBarHtml;
}

function createDualPost(data) {
	let postsHTML = "";
	for (const posts of data) {
		postsHTML += `
			<div class="grid grid-cols-2 bg-base-200 border border-base-300 p-10 gap-10 rounded-box">
				${createSinglePostHtml(posts[0])}
				${createSinglePostHtml(posts[1])}
				<div class="col-span-full flex justify-between items-center gap-10">
					${getVoteScoreHtml(posts, 0)}
					${createProgressBarHtml(posts)}
					${getVoteScoreHtml(posts, 1)}
				</div>
			</div>
		`;
	}
	return postsHTML;
}

function fetchPosts(count) {
	fetch(`/post/get-dual?count=${count}`)
		.then((response) => response.json())
		.then((data) => {
			const posts = document.getElementById("posts");
			posts.innerHTML += createDualPost(data);
		})
		.catch((error) => {
			console.error("Error fetching posts:", error);
		});
}
