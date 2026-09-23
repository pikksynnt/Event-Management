import { PrismaClient } from "@prisma/client";
import bcrypt from "bcryptjs";

const prisma = new PrismaClient();

async function main() {
  console.log("Seeding database...");

  // Seed Event Manager User
  const passwordHash = await bcrypt.hash("password123", 10);

  const manager = await prisma.user.upsert({
    where: { email: "manager@eo.com" },
    update: {
      name: "Budi Santoso",
      passwordHash,
      role: "event_manager",
    },
    create: {
      name: "Budi Santoso",
      email: "manager@eo.com",
      passwordHash,
      role: "event_manager",
    },
  });

  console.log(`Created/Updated user: ${manager.name} (${manager.email})`);

  // Clear existing events for a clean seed run
  await prisma.event.deleteMany({});

  // Seed 3 Realistic Sample Events
  const events = [
    {
      title: "Annual Corporate Gala",
      eventType: "Corporate",
      description:
        "Malam penghargaan dan apresiasi tahunan seluruh jajaran direksi dan staf korporasi dengan gala dinner serta pertunjukan seni.",
      startDate: new Date("2026-11-15T18:00:00Z"),
      endDate: new Date("2026-11-15T23:00:00Z"),
      estimatedGuests: 350,
      status: "submitted",
      rejectionReason: null,
    },
    {
      title: "Wedding Celebration",
      eventType: "Pernikahan",
      description:
        "Resepsi pernikahan eksklusif bertema modern botanical garden dengan jamuan prasmanan lengkap dan hiburan akustik.",
      startDate: new Date("2026-10-20T10:00:00Z"),
      endDate: new Date("2026-10-20T15:00:00Z"),
      estimatedGuests: 500,
      status: "approved",
      rejectionReason: null,
    },
    {
      title: "Product Launch",
      eventType: "Pameran & Peluncuran",
      description:
        "Peluncuran produk smartphone flagship terbaru dengan sesi live demo, booth interaktif, dan konferensi pers bersama media nasional.",
      startDate: new Date("2026-12-05T13:00:00Z"),
      endDate: new Date("2026-12-05T17:00:00Z"),
      estimatedGuests: 200,
      status: "rejected",
      rejectionReason:
        "Kapasitas venue yang diajukan tidak sesuai dengan standar keselamatan dan kelengkapan dokumen teknis belum memenuhi regulasi.",
    },
  ];

  for (const eventData of events) {
    const createdEvent = await prisma.event.create({
      data: eventData,
    });
    console.log(`Created event: ${createdEvent.title} [Status: ${createdEvent.status}]`);
  }

  console.log("Seeding completed successfully.");
}

main()
  .catch((e) => {
    console.error("Error during seeding:", e);
    process.exit(1);
  })
  .finally(async () => {
    await prisma.$disconnect();
  });
