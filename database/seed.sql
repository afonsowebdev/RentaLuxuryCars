-- ============================================================
-- RentaLuxuryCars — Dados Iniciais (Seed)
-- ============================================================

USE luxdrive;

-- Utilizador admin (password: LuxDrive2025!)
INSERT INTO utilizadores (nome, apelido, email, telefone, password_hash, tipo, ativo, email_verificado)
VALUES ('Admin', 'LuxDrive', 'admin@luxdrive.pt', '+351910000000',
        '$2y$12$2WptJynzgye8IZjaj7Ww1OsUVfL/AJQx00VwIaZTNwnVmpyxfhME.',
        'admin', 1, 1);

-- Veículos
INSERT INTO veiculos
(slug, marca, modelo, ano, categoria, preco_dia, preco_semana, preco_fim_semana, cavalos, motor, transmissao, lugares, velocidade_maxima, aceleracao, cor_exterior, cor_interior, descricao, caracteristicas, imagem_principal, imagens, disponivel, destaque)
VALUES
('lamborghini-huracan', 'Lamborghini', 'Huracán EVO', 2024, 'supercar', 1200.00, 7200.00, 2600.00, 640, 'V10 5.2L', 'automatico', 2, 325, '2.9s 0-100km/h',
 'Amarelo Giallo', 'Preto Alcântara',
 'O Huracán EVO representa a essência da Lamborghini: um V10 naturalmente aspirado, som visceral e uma condução que desafia os limites da física. Perfeito para quem procura adrenalina pura nas estradas portuguesas.',
 JSON_ARRAY('Tração Integral', 'Suspensão Magnética', 'Sistema LDVI', 'Escape Esportivo', 'Bancos em Carbono', 'Sistema de Som Premium'),
 'assets/images/cars/lamborghini-huracan/1.svg',
 JSON_ARRAY('assets/images/cars/lamborghini-huracan/1.svg','assets/images/cars/lamborghini-huracan/2.svg','assets/images/cars/lamborghini-huracan/3.svg','assets/images/cars/lamborghini-huracan/4.svg'),
 1, 1),

('ferrari-roma', 'Ferrari', 'Roma', 2023, 'gran_turismo', 950.00, 5700.00, 2100.00, 620, 'V8 3.9L Twin-Turbo', 'dct', 2, 320, '3.4s 0-100km/h',
 'Vermelho Rosso Corsa', 'Beige Cuoio',
 'Elegância atemporal encontra performance italiana. O Ferrari Roma é o Gran Turismo perfeito para viagens sofisticadas ao longo da costa portuguesa, combinando conforto de luxo com a alma desportiva Ferrari.',
 JSON_ARRAY('Modo Race', 'Bancos em Pele Premium', 'Sistema JBL Premium', 'Teto Panorâmico', 'Assistente de Condução', 'Câmara 360°'),
 'assets/images/cars/ferrari-roma/1.svg',
 JSON_ARRAY('assets/images/cars/ferrari-roma/1.svg','assets/images/cars/ferrari-roma/2.svg','assets/images/cars/ferrari-roma/3.svg','assets/images/cars/ferrari-roma/4.svg'),
 1, 1),

('porsche-911-turbo', 'Porsche', '911 Turbo S', 2024, 'supercar', 850.00, 5100.00, 1900.00, 650, 'Flat-6 3.8L Bi-Turbo', 'pdk', 4, 330, '2.7s 0-100km/h',
 'Preto Jet Black', 'Preto com costuras douradas',
 'O 911 Turbo S é a definição de versatilidade extrema: tão capaz numa pista de circuito como numa viagem diária. Tração integral e precisão de engenharia alemã ao seu dispor.',
 JSON_ARRAY('Tração Integral PTM', 'Suspensão PASM', 'Travões Cerâmicos', 'Sport Chrono', 'Bose Surround Sound', 'Assentos Aquecidos e Ventilados'),
 'assets/images/cars/porsche-911-turbo/1.svg',
 JSON_ARRAY('assets/images/cars/porsche-911-turbo/1.svg','assets/images/cars/porsche-911-turbo/2.svg','assets/images/cars/porsche-911-turbo/3.svg','assets/images/cars/porsche-911-turbo/4.svg'),
 1, 1),

('bentley-continental', 'Bentley', 'Continental GT', 2023, 'gran_turismo', 1050.00, 6300.00, 2300.00, 550, 'W12 6.0L Twin-Turbo', 'automatico', 4, 335, '3.6s 0-100km/h',
 'Azul Sequin Blue', 'Bronze com Piano Black',
 'Artesanato britânico ao mais alto nível. O Continental GT combina potência descomunal com um interior que redefine o conceito de luxo automóvel, ideal para clientes exigentes.',
 JSON_ARRAY('Rotating Display', 'Naim Audio Premium', 'Bancos Massajadores', 'Suspensão Pneumática', 'Modo Todo-o-Terreno', 'Iluminação Ambiente'),
 'assets/images/cars/bentley-continental/1.svg',
 JSON_ARRAY('assets/images/cars/bentley-continental/1.svg','assets/images/cars/bentley-continental/2.svg','assets/images/cars/bentley-continental/3.svg','assets/images/cars/bentley-continental/4.svg'),
 1, 0),

('rolls-royce-ghost', 'Rolls-Royce', 'Ghost', 2024, 'berlina_luxo', 1200.00, 7200.00, 2600.00, 571, 'V12 6.75L Twin-Turbo', 'automatico', 4, 250, '4.8s 0-100km/h',
 'Branco Arctic White', 'Ghost White com detalhes em Starlight',
 'A quintessência do luxo automóvel. O Ghost oferece uma experiência de condução silenciosa e majestosa, com o icónico Spirit of Ecstasy a guiar o caminho para eventos e ocasiões inesquecíveis.',
 JSON_ARRAY('Starlight Headliner', 'Suspensão Planar', 'Bancos Reclináveis Traseiros', 'Champagne Cooler', 'Isolamento Acústico Total', 'Espírito do Êxtase Retráctil'),
 'assets/images/cars/rolls-royce-ghost/1.svg',
 JSON_ARRAY('assets/images/cars/rolls-royce-ghost/1.svg','assets/images/cars/rolls-royce-ghost/2.svg','assets/images/cars/rolls-royce-ghost/3.svg','assets/images/cars/rolls-royce-ghost/4.svg'),
 1, 1),

('mclaren-720s', 'McLaren', '720S', 2023, 'supercar', 1100.00, 6600.00, 2400.00, 720, 'V8 4.0L Twin-Turbo', 'dct', 2, 341, '2.9s 0-100km/h',
 'Azul Papaya', 'Preto Alcântara com costuras laranja',
 'Engenharia de Fórmula 1 aplicada à estrada. As portas dihedral e o chassis em fibra de carbono fazem do 720S uma obra-prima de performance britânica com um design absolutamente marcante.',
 JSON_ARRAY('Portas Dihedral', 'Chassis Carbono MonoCage II', 'Suspensão Proactive Chassis Control', 'Modo Variable Drift', 'Bowers & Wilkins Audio', 'Câmara de Visão Traseira'),
 'assets/images/cars/mclaren-720s/1.svg',
 JSON_ARRAY('assets/images/cars/mclaren-720s/1.svg','assets/images/cars/mclaren-720s/2.svg','assets/images/cars/mclaren-720s/3.svg','assets/images/cars/mclaren-720s/4.svg'),
 1, 1),

('aston-martin-db11', 'Aston Martin', 'DB11', 2023, 'gran_turismo', 900.00, 5400.00, 2000.00, 630, 'V12 5.2L Twin-Turbo', 'automatico', 4, 322, '3.9s 0-100km/h',
 'Verde British Racing Green', 'Tan com Piano Black',
 'O DB11 é a personificação do glamour britânico — o carro escolhido por James Bond. Uma combinação irresistível de potência V12, design escultural e conforto de viagem de longa distância.',
 JSON_ARRAY('Modo GT/Sport/Sport+', 'Bang & Olufsen Audio', 'Bancos em Pele Premium', 'Head-Up Display', 'Adaptive Damping', 'Cruise Control Adaptativo'),
 'assets/images/cars/aston-martin-db11/1.svg',
 JSON_ARRAY('assets/images/cars/aston-martin-db11/1.svg','assets/images/cars/aston-martin-db11/2.svg','assets/images/cars/aston-martin-db11/3.svg','assets/images/cars/aston-martin-db11/4.svg'),
 1, 0),

('maserati-ghibli', 'Maserati', 'Ghibli Trofeo', 2023, 'berlina_luxo', 650.00, 3900.00, 1450.00, 580, 'V8 3.8L Twin-Turbo', 'automatico', 5, 326, '4.3s 0-100km/h',
 'Cinzento Grigio Maratea', 'Nero com Trident bordado',
 'A berlina desportiva italiana com a alma de um Ferrari — o motor é produzido em Maranello. O Ghibli Trofeo une elegância, espaço para 5 pessoas e um som de escape inconfundível.',
 JSON_ARRAY('Motor Ferrari-Tuned', 'Sistema Skyhook', 'Harman Kardon Premium', 'Bancos Desportivos em Pele', 'Modo Corsa', 'Assistente de Estacionamento'),
 'assets/images/cars/maserati-ghibli/1.svg',
 JSON_ARRAY('assets/images/cars/maserati-ghibli/1.svg','assets/images/cars/maserati-ghibli/2.svg','assets/images/cars/maserati-ghibli/3.svg','assets/images/cars/maserati-ghibli/4.svg'),
 1, 0);

-- Extras de reserva
INSERT INTO extras (slug, nome, descricao, preco_dia, icone, ativo) VALUES
('gps-premium', 'GPS Premium', 'Sistema de navegação premium com trânsito em tempo real', 15.00, 'fa-solid fa-location-crosshairs', 1),
('condutor-adicional', 'Condutor Adicional', 'Adicione um segundo condutor autorizado à reserva', 25.00, 'fa-solid fa-user-plus', 1),
('seguro-premium', 'Seguro Premium', 'Cobertura total sem franquia, incluindo pneus e jantes', 60.00, 'fa-solid fa-shield-halved', 1),
('entrega-aeroporto', 'Entrega no Aeroporto', 'Entrega e recolha em qualquer aeroporto de Portugal', 45.00, 'fa-solid fa-plane-arrival', 1),
('baby-seat', 'Cadeira de Bebé', 'Cadeira de segurança homologada para crianças', 12.00, 'fa-solid fa-baby-carriage', 1);
