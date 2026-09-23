"use client";

import React, { useState } from "react";
import { useRouter } from "next/navigation";
import { approveEventAction, rejectEventAction } from "@/app/actions/events";
import { Button } from "@/components/Button";
import { FeedbackAlert } from "@/components/FeedbackAlert";
import { CheckCircle2, XCircle, AlertTriangle } from "lucide-react";

interface EventDetailActionsProps {
  eventId: number;
  currentStatus: string;
}

export function EventDetailActions({
  eventId,
  currentStatus,
}: EventDetailActionsProps) {
  const router = useRouter();
  const [isRejectModalOpen, setIsRejectModalOpen] = useState(false);
  const [rejectionReason, setRejectionReason] = useState("");
  const [isLoading, setIsLoading] = useState(false);
  const [feedback, setFeedback] = useState<{
    type: "success" | "error";
    message: string;
  } | null>(null);

  async function handleApprove() {
    setFeedback(null);
    setIsLoading(true);
    const result = await approveEventAction(eventId);
    setIsLoading(false);

    if (result.error) {
      setFeedback({ type: "error", message: result.error });
    } else {
      setFeedback({
        type: "success",
        message: "Event berhasil disetujui.",
      });
      router.refresh();
    }
  }

  async function handleRejectSubmit(e: React.FormEvent) {
    e.preventDefault();
    if (!rejectionReason.trim()) {
      setFeedback({
        type: "error",
        message: "Alasan penolakan wajib diisi.",
      });
      return;
    }

    setFeedback(null);
    setIsLoading(true);
    const result = await rejectEventAction(eventId, rejectionReason);
    setIsLoading(false);

    if (result.error) {
      setFeedback({ type: "error", message: result.error });
    } else {
      setIsRejectModalOpen(false);
      setRejectionReason("");
      setFeedback({
        type: "success",
        message: "Event telah ditolak beserta alasan penolakan yang disimpan.",
      });
      router.refresh();
    }
  }

  return (
    <div className="space-y-4">
      {feedback && (
        <FeedbackAlert
          type={feedback.type}
          message={feedback.message}
          onClose={() => setFeedback(null)}
        />
      )}

      {/* Action Buttons */}
      <div className="flex flex-wrap items-center gap-3">
        <Button
          type="button"
          variant="success"
          size="md"
          isLoading={isLoading}
          onClick={handleApprove}
          disabled={isLoading || currentStatus === "approved"}
          className="gap-2"
        >
          <CheckCircle2 className="w-4 h-4" />
          <span>{currentStatus === "approved" ? "Sudah Disetujui" : "Setujui Event"}</span>
        </Button>

        <Button
          type="button"
          variant="danger"
          size="md"
          isLoading={isLoading}
          onClick={() => {
            setFeedback(null);
            setIsRejectModalOpen(true);
          }}
          disabled={isLoading || currentStatus === "rejected"}
          className="gap-2"
        >
          <XCircle className="w-4 h-4" />
          <span>{currentStatus === "rejected" ? "Sudah Ditolak" : "Tolak Event"}</span>
        </Button>
      </div>

      {/* Reject Modal */}
      {isRejectModalOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
          <div className="bg-white rounded-xl border border-slate-200 shadow-xl max-w-lg w-full p-6 space-y-4">
            <div className="flex items-center gap-3">
              <div className="p-2 rounded-lg bg-rose-100 text-rose-700">
                <AlertTriangle className="w-5 h-5" />
              </div>
              <div>
                <h3 className="text-base font-bold text-slate-900">
                  Konfirmasi Penolakan Event
                </h3>
                <p className="text-xs text-slate-500">
                  Tindakan ini memerlukan alasan penolakan yang jelas.
                </p>
              </div>
            </div>

            <form onSubmit={handleRejectSubmit} className="space-y-4">
              <div>
                <label
                  htmlFor="rejectionReason"
                  className="block text-sm font-medium text-slate-700 mb-1"
                >
                  Alasan Penolakan <span className="text-rose-500">*</span>
                </label>
                <textarea
                  id="rejectionReason"
                  rows={4}
                  required
                  value={rejectionReason}
                  onChange={(e) => setRejectionReason(e.target.value)}
                  placeholder="Tuliskan alasan penolakan event secara spesifik (misal: jadwal bertabrakan, dokumen tidak lengkap, dsb)..."
                  className="w-full rounded-lg border border-slate-300 p-3 text-sm text-slate-900 placeholder-slate-400 focus:border-rose-600 focus:outline-none focus:ring-1 focus:ring-rose-600 transition-colors"
                />
              </div>

              <div className="flex items-center justify-end gap-3 pt-2">
                <Button
                  type="button"
                  variant="outline"
                  size="md"
                  disabled={isLoading}
                  onClick={() => {
                    setIsRejectModalOpen(false);
                    setRejectionReason("");
                  }}
                >
                  Batal
                </Button>
                <Button
                  type="submit"
                  variant="danger"
                  size="md"
                  isLoading={isLoading}
                >
                  Simpan & Tolak Event
                </Button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
}
