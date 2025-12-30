
console.log("    ____           __  __                               ____           ");
console.log("   / __ )__  __   / / / /___ _____  __  ______ _____   /  _/____ ____ _");
console.log("  / __  / / / /  / /_/ / __ `/_  / / / / / _  \/ ___/   / // __  \/ __ `/");
console.log(" / /_/ / /_/ /  / __  / /_/ / / /_/ /_/ /  __/ /     _/ // / / / /_/ / ");
console.log("/_____/\___, /  /_/ /_/\___,_/ /___/\___,_/\____/_/     /___/_/ /_/\___,  (.)");
console.log("      /____/                                                  /____/   ");
const moment = require("moment-timezone");
const { Client, Location, Poll, List, Buttons, LocalAuth } = require("./whatsapp/index");
const Database = require("./database.js")
const readline = require("readline");
const rl = readline.createInterface({
  input: process.stdin,
  output: process.stdout
});
const client = new Client({
    authStrategy: new LocalAuth(),
    puppeteer: { 
        headless: false,
    },
});
client.initialize();
client.on("qr", async (qr) => {
    // NOTE: This event will not be fired if a session is specified.
    console.log("QR RECEIVED", qr);
});
client.on("code", (code) => {
    console.log("Pairing code:",code);
});
client.on("authenticated", () => {
    console.log("AUTHENTICATED");
});
client.on("auth_failure", msg => {
    // Fired if session restore was unsuccessful
    console.error("AUTHENTICATION FAILURE", msg);
});
client.on("ready", async () => {
	console.log("READY");
    const debugWWebVersion = await client.getWWebVersion();
    console.log(`WWebVersion = ${debugWWebVersion}`);
	////////////////////////////////////

	let db = new Database("false")
	const id_location = 1;
	const id_estatus = 2;
	const n_user_id=1
	const numbers = ["7341346283"];
	const message = `Prueba de funcionalidad`;
	const idParceIn = `1,2,3`;
	let iconBot= ``;
	let tipoMessage =``;
	switch (id_estatus) {
		case 1:
			iconBot= `🤖 `;
			tipoMessage =`Nuevo`;
		break;
		case 2:
			iconBot= `🔔 `;
			tipoMessage =`Recordatorio Mensajes Enviados`;
		break;
		case 5:
			iconBot= `📢 `;
			tipoMessage =`Recordatorio Paquetes Confirmados`;
		break;
	}
	// Mostrar números del arreglo en pantalla
	console.log("--------------------------------------");
	console.log(`Formato del mensaje: ${tipoMessage}`);
	console.log(message);
	console.log("--------------------------------------");
	console.log("Números de teléfono a los que se enviará el mensaje:");
	numbers.forEach((number, index) => {
	  console.log(`${index + 1}. ${number}`);
	});
	// Solicitar al usuario si desea continuar
	rl.question("Desea continuar? (s/n): ",  async (answer) => {
	  if (answer.toLowerCase() === "s") {
		let ids =  0;
		for (let i = 0; i < numbers.length; i++) {
			const number = numbers[i];
			const sql =`SELECT 
			cc.phone,
			cc.id_contact_type,
			GROUP_CONCAT(p.id_package) AS ids,
			GROUP_CONCAT('*(',p.folio,')-',p.tracking,'*' SEPARATOR '\n') AS folioGuias 
			FROM package p 
			INNER JOIN cat_contact cc ON cc.id_contact=p.id_contact 
			WHERE 
			p.id_location IN (${id_location}) 
			AND p.id_status IN (${id_estatus}) 
			AND cc.phone IN(${number}) 
			AND p.id_cat_parcel IN(${idParceIn}) 
			GROUP BY cc.phone`
			const data        = await db.processDBQueryUsingPool(sql)
			const rst         = JSON.parse(JSON.stringify(data))
			ids               = rst[0] ? rst[0].ids : 0;
			let idContactType = rst[0] ? rst[0].id_contact_type : 0;
			let folioGuias    = rst[0] ? rst[0].folioGuias : 0;
			let fullMessage   = `${message}`;
			let sid = "";
			if(ids!=0){
				let registros = folioGuias ? folioGuias.split('\n').filter(Boolean) : [];
				let totalRegistros = registros.length;
				fullMessage = `${message} \n*Total:${totalRegistros}*\n*(Folio)-Guía:*\n${folioGuias}`;
	
				let newStatusPackage = 1;
				let id_contact_type  = 3;
				let logWhats      = null;

				if(idContactType==2){ //WhatsApp
					const chatId = "521"+number+ "@c.us";
					let rst = await sendMessageWhats(client, chatId, fullMessage, iconBot);
					sid = `${rst.descMsj} ::Whatsapp Registrado`;
					logWhats = rst.details;
					newStatusPackage = rst.status ? (id_estatus == 5 ? 5 : 2) : 6;
					id_contact_type  = 2;
				}else{
					const number_details = await client.getNumberId(number); // get mobile number details
					if (number_details) {
						let rst = await sendMessageWhats(client, number_details._serialized, fullMessage,iconBot);
						sid =`${rst.descMsj}`;
						logWhats = rst.details;
						newStatusPackage = rst.status ? (id_estatus == 5 ? 5 : 2) : 6;
						// if(ids!=0){
							const lastMessage = moment().tz("America/Mexico_City").format("YYYY-MM-DD HH:mm:ss");
							const sqlUpdateTypeContact = `UPDATE cat_contact 
							SET id_contact_type=2, lastMessage='${lastMessage}'
							WHERE id_location=${id_location} AND phone='${number}' AND id_contact_type=1`
							await db.processDBQueryUsingPool(sqlUpdateTypeContact)
						// }
					} else {
						sid = `${number}, Número de móvil no registrado`
						logWhats = sid;
						newStatusPackage = 6
					}
					//if (i < numbers.length - 1) {
						//await sleep(1000); // tiempo de espera en segundos entre cada envío
					//}
				}

			//if(ids!=0){
				const listIds = ids.split(",");
				const nDate = moment().tz("America/Mexico_City").format("YYYY-MM-DD HH:mm:ss");
				let fullLog=`${sid}, ${logWhats}`;
				for (let i = 0; i < listIds.length; i++) {
					const id_package = listIds[i];
					const sqlSaveNotification = `INSERT INTO notification 
					(id_location,n_date,n_user_id,message,id_contact_type,sid,id_package) 
					VALUES 
					(${id_location},'${nDate}',${n_user_id},'${fullMessage}',${id_contact_type},'${fullLog}',${id_package})`
					await db.processDBQueryUsingPool(sqlSaveNotification);

					const sqlLogger = `INSERT INTO logger 
					(datelog, id_package, id_user, new_id_status, old_id_status, desc_mov) 
					VALUES 
					('${nDate}', ${id_package}, ${n_user_id}, ${newStatusPackage}, ${id_estatus}, 'Envío de Mensaje WhatsApp')`
					await db.processDBQueryUsingPool(sqlLogger)

					const sqlUpdatePackage = `UPDATE package SET 
					n_date = '${nDate}', n_user_id = '${n_user_id}', id_status=${newStatusPackage} 
					WHERE id_package IN (${id_package})`
					await db.processDBQueryUsingPool(sqlUpdatePackage)
				}
			}else{
				sid = ` Número ${number} sin guías para procesar, mensaje no enviado`;
			}

			console.log(`${i + 1} - ${sid}`);
			// Delay aleatorio entre 2 y 6 segundos entre mensajes
			await randomSleep(3000, 8000);

			// Cada 20 mensajes, pausa larga de 1 a 3 minutos
			await pauseEveryN(i + 1, 20, Math.floor(Math.random() * (180000 - 60000 + 1)) + 60000);
		}
		console.log("Proceso finalizado...");
	  } else {
		console.log("Proceso de envío de mensajes cancelado");
	  }
	  rl.close();

	});

	////////////////////////////////////
	client.pupPage.on("pageerror", function(err) {
        console.log("Page error: " + err.toString());
    });
    client.pupPage.on("error", function(err) {
        console.log("Page error: " + err.toString());
    });

});
async function randomSleep(minMs, maxMs) {
    const ms = Math.floor(Math.random() * (maxMs - minMs + 1)) + minMs;
    console.log(`⏳ Esperando ${ms / 1000} segundos antes del siguiente mensaje...`);
    return new Promise(resolve => setTimeout(resolve, ms));
}

async function pauseEveryN(count, n, pauseMs) {
    if (count > 0 && count % n === 0) {
        console.log(`⏸ Pausa larga de ${(pauseMs / 1000 / 60).toFixed(2)} minutos después de ${count} mensajes...`);
        await sleep(pauseMs);
    }
}
function sleep(ms) {
	return new Promise(resolve => setTimeout(resolve, ms));
}
async function sendMessageWhats(client, chatId, fullMessage, iconBot) {
	let rstSent = null;
    try {
        // Intentar enviar el mensaje
        const response = await client.sendMessage(chatId, `${iconBot} ${fullMessage}`);
        // Si el response tiene el campo id, consideramos que el mensaje fue enviado
        if (response && response.id) {
			let details = `success id:${response.id.id}, phone:${response.to}, timestamp:${response.timestamp}`;
			rstSent = {
				status:true,
				descMsj:`Mensaje enviado exitosamente a ${response.to}`,
				details:details
			}
        } else {
            // Si no hay un id en el response, consideramos que el envío falló
			rstSent = {
				status:false,
				descMsj:`Error en el envío, no se recibió un ID del mensaje`,
				details:`No se recibio respuesta del servicio`
			}
        }
    } catch (error) {
		rstSent = {
			status:false,
			descMsj:`Error al enviar mensaje a ${chatId}`,
			details:`error:${error}`
		}
    }
	return rstSent
}