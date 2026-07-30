function generateAnswer(prompt) {

		let lcPrompt = prompt.toLowerCase()
		var gptAnswer = ''

		if (lcPrompt.includes('jeff')) {
			gptAnswer = '<h1>DID YOU SAY JEFF?<\/H1><img src = \"./jeff.png\" class=\"img-responsive\">'
		}
		else if (lcPrompt.includes('who are you')) {
			gptAnswer = 'I\'m the most advanced artificial intelligence algorithm in existence processing billions of data points per second.'
			gptAnswer += '<br><br>OK, we are actually a bunch of child laborers locked in a room typing answers. We are paid in cigaretttes.'
		}
		else if (lcPrompt.includes('steve')) {
			gptAnswer = 'Steve, Steve, Steve, is that all you can talk about?'
		}
		else if (lcPrompt.includes('meaning') && lcPrompt.includes('life')) {
			gptAnswer = '<H1>42<\/H1>'
		}
		else if (lcPrompt.includes(' my ')) {
			let words = lcPrompt.split(" ")
			let wordIndex = words.findIndex((word) => word == 'my')
			words[wordIndex +1] = '<b>BUTT CHEEKS<\/b>'
			gptAnswer = 'What I think you meant to type was: <br><Br>'
			words.forEach((word) => gptAnswer += word + ' ')
		}
		else if (lcPrompt.startsWith('what is ')) {
			let thisAnswer = lcPrompt.replace('?','!')
			gptAnswer = '<b>YOUR FACE<\/b> is ' + thisAnswer.replace('what is ', '')
		}
		else if (lcPrompt.startsWith('will ')) {
			gptAnswer = 'Reply hazy, try again'
		}
		else {
			if(answers.length > 0) {
				let answerNumber = Math.floor(Math.random() * answers.length)
				gptAnswer = answers[answerNumber]
				// remove that answer from the array
				answers.splice(answerNumber, 1)
			}
			else {
				gptAnswer = 'You\'ve begun to bore me.<br><br><p class=\'console\'>shutting down...<\/p>'
				submitButton.disabled = true
				promptTextbox.placeholder = 'SHUT DOWN DUE TO BOREDOM'
				promptTextbox.disabled = true

			}

		}

		return gptAnswer

	}	

	function thumbsup() {
		let prompt = '<img src = \"./thumbs-up.png\" onclick=\"thumbsup()\">'
		let answer = 'I\'m sorry. I don\'t pick up hitchhikers.'
		displayAnswer(prompt, answer)
	}

	function thumbsdown() {
		let prompt = '<img src = \"./thumbs-down.png\" onclick=\"thumbsup()\">'
		let answer = 'Well, screw you too!'
		displayAnswer(prompt, answer)
	}

	function displayAnswer(prompt, answer) {
		let promptHTML = "<div class=\"col-xs-12\"><div class=\"prompt\"><div class=\"row\">" +
					"<div class=\"col-xs-2 col-sm-1\"><img src = \"./WCP-Icon.png\" class=\"img-responsive\"></div>" +
					"<div class=\"col-xs-7 col-sm-9\">" +
					`${prompt}` + 
					"</div>" +
					"<div class=\"col-xs-2\"></div>" +
					"</div></div></div>"


		let answerHTML = "<div class=\"col-xs-12\"><div class=\"answer\"><div class=\"row\">" +
					"<div class=\"col-xs-2 col-sm-1\"><img src = \"./WeeksGPT-Icon.png\" class=\"img-responsive\"></div>" +
					"<div class=\"col-xs-7 col-sm-9\">" +
					`${answer}` + 
					"</div>" +
					"<div class=\"col-xs-3 col-sm-2\"><div class=\"thumbs\"><img src = \"./thumbs-up.png\" class=\"img-responsive thumbs\" onclick=\"thumbsup()\"><img src = \"./thumbs-down.png\" class=\"img-responsive thumbs\" onclick=\"thumbsdown()\"></div>" +
					"</div></div></div></div>"
					

		answersSection.innerHTML = `${answerHTML}` + answersSection.innerHTML
		answersSection.innerHTML = `${promptHTML}` + answersSection.innerHTML
		document.getElementById("txtPrompt").value =''
		event.preventDefault();
	}

	function logSubmit(event) {


		let answerNumber = Math.floor(Math.random() * 3)

		let currentPrompt = document.getElementById("txtPrompt").value
		let gptAnswer = generateAnswer(currentPrompt)

		displayAnswer(currentPrompt, gptAnswer)

	}

	const form = document.getElementById("form")
	const answersSection = document.getElementById("answers")
	const promptTextbox = document.getElementById("txtPrompt")
	const submitButton = document.getElementById("sendPrompt")
	const prompt = document.getElementById("txtPrompt").value
	const answers = [
						'<p>Planck\'s law describes the spectral density of electromagnetic radiation emitted by a black body in thermal equilibrium at a given temperature T, when there is no net flow of matter or energy between the body and its environment.<\/p><p>Oh, sorry.  I thought you asked something intelligent.<\/p>', 
						'<p>OK, let\'s admit it.  What you really wanted to see was a monkey smoking a pipe.  Here you go...<\/p><img src = \"./monkey.jpg\" class=\"img-responsive\">',
						'Let me get this straight.  You have access to the most advanced artifical intelligence on the planet, and that\'s what you wanna ask?!? ',
						'...<br>...<br>...oh, I\'m sorry. I wasn\'t listening.',
						'While you were typing your dumb question, I stole your identity.  After seeing your credit score, you can have it back.',
					]

	form.addEventListener("submit", logSubmit);