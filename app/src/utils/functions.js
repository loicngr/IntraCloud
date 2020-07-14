/**
 * Supprime le dernier caractère d'une chaîne de caractères
 * @param {string} str
 * @return {string} Nouvelle chaîne sans le dernier caractère
 */
function removeLastChar(str) {
    return str.substring(0, str.length - 1);
}

/**
 * Formate une Date
 * @param {string} str La date
 * @return {string} Date formaté
 */
function formatDate(str) {
    const date = new Date(str);

    let month = (date.getUTCMonth() + 1).toLocaleString();
    month = month.length === 1 ? '0' + month : month;

    let day = date.getUTCDate().toLocaleString();
    day = day.length === 1 ? '0' + day : day;

    let hour = date.getHours().toString();
    hour = hour.length === 1 ? '0' + hour : hour;

    let minute = date.getMinutes().toString();
    minute = minute.length === 1 ? '0' + minute : minute;

    return `${date.getFullYear()}/${month}/${day} - ${hour}:${minute}`;
}

/**
 * Formate une taille en Octet
 * @param {string} str
 * @returns {string}
 */
function formatSize(str) {
    const Length = str.length;
    let result;

    if (Length <= 3) {
        result = str;
    } else if (Length === 4 && Length < 5) {
        result = str.substring(0, 1) + 'K';
    } else if (Length > 4 && Length < 6) {
        result = str.substring(0, 2) + 'K';
    } else if (Length === 6) {
        result = str.substring(0, 3) + 'K';
    } else if (Length > 6 && Length <= 9) {
        result = str.substring(0, 2) + 'M';
    }

    return result + ' ' + 'Bytes';
}

/**
 * Retourne une class css en fonction de la validité d'un champs formulaire
 * @param {string} formContent
 * @param {string} getFormContent
 * @param {string} validClass
 * @param {string} errorClass
 * @param {string} defaultClass
 * @returns {string}
 */
function fieldFormValidation(
    formContent,
    getFormContent,
    validClass = 'border-green',
    errorClass = 'border-red',
    defaultClass = 'border-gray'
) {
    return formContent === '' ? defaultClass : getFormContent ? validClass : errorClass;
}

/**
 * Convertit un Base64 en Blob
 * @param {string} dataURI
 * @param {string} mime
 * @returns {Blob}
 */
function b64ToBlob(dataURI, mime) {
    const byteString = atob(dataURI.split(',')[1]);
    const ab = new ArrayBuffer(byteString.length);
    const ia = new Uint8Array(ab);

    const byteLength = byteString.length;
    for (let i = 0; i < byteLength; i++) {
        ia[i] = byteString.charCodeAt(i);
    }
    return new Blob([ab], { type: mime });
}

/**
 * Decoder un Token JWT
 * @param {string} token
 */
function readTokenAuth(token) {
    if (!token) throw new Error('No token given');

    const jwtDecode = require('jwt-decode');

    return jwtDecode(token);
}

/**
 * Cette condition vérifie si un nom de fichier est constitué
 * de plusieurs espaces (exemple: "monfichier   (1).jpg"), si oui, on les supprime tous sauf un, celui du milieu.
 * Et à la fin on supprime les retours de lignes si il y en a.
 * */
function removeSpacesBetweenStr(str) {
    let newStr = '';
    const splitedStr = str.split(' ');
    let cpt = splitedStr.length;
    splitedStr.forEach((char) => {
        if (char === '') {
            if (cpt > 3) cpt--;
            else newStr += ' ';
        } else newStr += char;
    });
    return newStr.replace(/(\r\n|\n|\r)/gm, '');
}

/**
 * La fonction reçoit en paramètre un tableau avec deux clés,
 * 			1. filesType - Qui result de la commande "file" dans Unix
 * 			2. lsResult - Qui result de la commande "ls" dans Unix
 *
 * Avec la clé "filesType" on peut donc déterminer si l'élement et un dossier,
 * un fichier, une image ou encore un lien symbolique.
 *
 * Et avec la clé "lsResult" on peut récupérer plus d'informations sur l'élément.
 * @param array
 * @return {Promise<[
 * 			{
		        permissions: null,
				user: null,
				group: null,
				size: null,
				month: null,
				day: null,
				hour: null,
				name: null,
				mime: null,
				type: null
 *         	}
 * ]>}
 */
async function filterUnixLS(array) {

	const elements = [];

	/**
	 * On formate la réponse de la commande ls -lh et file
	 * */
	let cpt = 0;
	for await (let row of array.filesType) {
		/**
		* Avant : /home/intracloud/testDossier: inode/directory
		* Après : ["/home/intracloud/testDossier", "inode/directory"]
		* */
		const rowSplitMime = row.split(': ').filter((elem) => elem !== '').map((element) => element.trim());

		let type = '';
		switch (rowSplitMime[1]) {
			case 'inode/directory':
				type = 'folder';
				break;
			case 'inode/symlink':
				// TODO - gérer les fichiers en liens symbolique
				type = 'symlink';
				break;
			default:
				type = 'file';
				if (rowSplitMime[0].match(/.(jpg|jpeg|png|gif|tiff|webp|bmp)$/i)) type = 'image';
				break;
		}

		const element = {
			permissions: null,
			user: null,
			group: null,
			size: null,
			month: null,
			day: null,
			hour: null,
			name: null,
			mime: rowSplitMime[1],
			type: type
		};

		const lsElement = await array.lsResult[cpt].split(' ').filter((elem) => elem !== '').map((element) => element.trim());

		/**
		 * Si le nom contient des espaces il serra sur plusieurs index du tableau après l'index 8
		 * On récupère les valeurs après l'index 8 pour les joindre entres elles puis effacer les espaces en trop.
		 * */
		let name = lsElement[8];
		if (type !== 'symlink') name = (lsElement.length > 9)? lsElement.map((val, i) => {if (i >= 8) return val }).join(' ').trim():lsElement[8];

		element.permissions = lsElement[0];
		element.user = lsElement[2];
		element.group = lsElement[3];
		element.size = lsElement[4];
		element.month = lsElement[5];
		element.day = lsElement[6];
		element.hour = lsElement[7];
		element.name = name;

		if (lsElement[8]) elements.push(element);

		cpt ++;
	}

	return elements;

}

export {
    removeLastChar,
    formatDate,
    formatSize,
    fieldFormValidation,
    b64ToBlob,
    readTokenAuth,
    removeSpacesBetweenStr,
	filterUnixLS
};
