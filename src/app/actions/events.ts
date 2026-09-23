"use server";

import prisma from "@/lib/prisma";
import { requireEventManager } from "@/lib/auth";
import { revalidatePath } from "next/cache";

export interface EventActionResult {
  success?: boolean;
  error?: string;
}

/**
 * Approve event action
 * Requires server-side Event Manager authorization
 */
export async function approveEventAction(
  eventId: number
): Promise<EventActionResult> {
  try {
    // Server-side role authorization check
    await requireEventManager();

    const existing = await prisma.event.findUnique({
      where: { id: eventId },
    });

    if (!existing) {
      return { error: "Event tidak ditemukan." };
    }

    await prisma.event.update({
      where: { id: eventId },
      data: {
        status: "approved",
        rejectionReason: null, // clear previous rejection reason if any
      },
    });

    revalidatePath("/dashboard/manager");
    revalidatePath(`/dashboard/manager/events/${eventId}`);

    return { success: true };
  } catch (error) {
    console.error("Error approving event:", error);
    return { error: "Gagal menyetujui event. Silakan coba kembali." };
  }
}

/**
 * Reject event action
 * Requires server-side Event Manager authorization and non-empty rejectionReason
 */
export async function rejectEventAction(
  eventId: number,
  rejectionReason: string
): Promise<EventActionResult> {
  try {
    // Server-side role authorization check
    await requireEventManager();

    const trimmedReason = rejectionReason?.trim();
    if (!trimmedReason) {
      return { error: "Alasan penolakan wajib diisi." };
    }

    const existing = await prisma.event.findUnique({
      where: { id: eventId },
    });

    if (!existing) {
      return { error: "Event tidak ditemukan." };
    }

    await prisma.event.update({
      where: { id: eventId },
      data: {
        status: "rejected",
        rejectionReason: trimmedReason,
      },
    });

    revalidatePath("/dashboard/manager");
    revalidatePath(`/dashboard/manager/events/${eventId}`);

    return { success: true };
  } catch (error) {
    console.error("Error rejecting event:", error);
    return { error: "Gagal menolak event. Silakan coba kembali." };
  }
}
